<?php

namespace App\Http\Controllers;

use App\Models\AiActivity;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AI\AgentContext;
use App\Services\AI\AiAgent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(private AiAgent $agent) {}

    /** Volledige AI-collega werkplek (prominente pagina). */
    public function page(Request $request)
    {
        return view('company.ai.index');
    }

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
        $model = $undo['model'] ?? null;

        // Generiek terugdraaien voor elk model dat de AI muteert, altijd binnen
        // het eigen bedrijf. Zacht verwijderde records worden meegenomen.
        if (is_string($model) && class_exists($model) && isset($undo['id'])) {
            $soft = in_array(SoftDeletes::class, class_uses_recursive($model), true);
            $record = ($soft ? $model::withTrashed() : $model::query())->find($undo['id']);

            if (! $record) {
                return response()->json(['error' => 'Het betreffende item bestaat niet meer.'], 422);
            }

            // Strikte bedrijfsscope: Company is het bedrijf zelf, andere modellen
            // hebben een company_id.
            $ownsIt = $model === \App\Models\Company::class
                ? ((int) $record->id === (int) $user->company_id)
                : ((int) ($record->company_id ?? 0) === (int) $user->company_id);
            abort_unless($ownsIt, 403);

            match ($undo['type'] ?? null) {
                'created' => $record->delete(),                                    // undo toevoegen => verwijderen
                'deleted' => $soft ? $record->restore() : null,                    // undo verwijderen => herstellen
                'updated' => $record->forceFill($undo['before'] ?? [])->save(),    // undo wijziging => oude waarden terug
                default => null,
            };
        }

        $activity->update(['undone_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
