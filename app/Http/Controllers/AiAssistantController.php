<?php

namespace App\Http\Controllers;

use App\Models\AiActivity;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\AI\AiAgent;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(private AiAgent $agent) {}

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer',
        ]);

        if (empty(config('ai.api_key'))) {
            return response()->json([
                'error' => 'De AI-assistent is nog niet geconfigureerd (ANTHROPIC_API_KEY ontbreekt).',
            ], 422);
        }

        $user = $request->user();

        $conversation = null;
        if (! empty($validated['conversation_id'])) {
            $conversation = AiConversation::where('company_id', $user->company_id)
                ->where('user_id', $user->id)
                ->find($validated['conversation_id']);
        }
        if (! $conversation) {
            $conversation = AiConversation::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'title' => mb_strimwidth($validated['message'], 0, 60, '...'),
            ]);
        }

        // History = prior turns + the new user message.
        $history = $conversation->messages->map(fn (AiMessage $m) => [
            'role' => $m->role,
            'content' => $m->content,
        ])->values()->all();
        $history[] = ['role' => 'user', 'content' => $validated['message']];

        $conversation->messages()->create(['role' => 'user', 'content' => $validated['message']]);

        $context = new AgentContext(
            companyId: $user->company_id,
            userId: $user->id,
            conversationId: $conversation->id,
        );

        try {
            $result = $this->agent->run($context, $history, $user->company?->name ?? 'je bedrijf');
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'De assistent gaf een fout: ' . $e->getMessage()], 500);
        }

        $reply = $result['reply'] !== '' ? $result['reply'] : 'Gedaan.';
        $conversation->messages()->create(['role' => 'assistant', 'content' => $reply]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'reply' => $reply,
            'activities' => collect($result['activities'])->map(fn (AiActivity $a) => [
                'id' => $a->id,
                'summary' => $a->summary,
            ])->values()->all(),
        ]);
    }

    public function undo(Request $request, AiActivity $activity)
    {
        $user = $request->user();
        abort_unless($activity->company_id === $user->company_id, 403);

        if ($activity->isUndone()) {
            return response()->json(['error' => 'Deze actie is al teruggedraaid.'], 422);
        }

        $undo = $activity->undo_data ?? [];

        if (($undo['model'] ?? null) === Car::class && isset($undo['id'])) {
            $car = Car::withTrashed()->where('company_id', $user->company_id)->find($undo['id']);

            if (! $car) {
                return response()->json(['error' => 'Het betreffende item bestaat niet meer.'], 422);
            }

            match ($undo['type'] ?? null) {
                'created' => $car->delete(),                                   // undo toevoegen => verwijderen
                'deleted' => $car->restore(),                                 // undo verwijderen => herstellen
                'updated' => $car->forceFill($undo['before'] ?? [])->save(),  // undo wijziging => oude waarden terug
                default => null,
            };
        }

        $activity->update(['undone_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
