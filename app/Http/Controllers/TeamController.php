<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $members = User::where('company_id', $companyId)
            ->orderByRaw("FIELD(role, 'owner', 'admin', 'sales', 'accountant')")
            ->orderBy('name')
            ->get();

        $invitations = TeamInvitation::where('company_id', $companyId)
            ->whereNull('accepted_at')
            ->latest()
            ->get();

        return view('company.team.index', [
            'members' => $members,
            'invitations' => $invitations,
            'roles' => Roles::ROLES,
            'assignable' => Roles::assignableRoles(),
        ]);
    }

    /** Invite a new team member: create a token and show a shareable link. */
    public function invite(Request $request)
    {
        $companyId = $request->user()->company_id;

        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'role' => ['required', Rule::in(array_keys(Roles::assignableRoles()))],
        ]);

        $email = mb_strtolower(trim($data['email']));

        if (User::where('company_id', $companyId)->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages(['email' => 'Dit e-mailadres hoort al bij een teamlid.']);
        }

        // Vervang een eventuele openstaande uitnodiging voor hetzelfde adres.
        TeamInvitation::where('company_id', $companyId)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->delete();

        $invitation = TeamInvitation::create([
            'company_id' => $companyId,
            'email' => $email,
            'role' => $data['role'],
            'invited_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Uitnodiging aangemaakt voor ' . $email . '. Deel de uitnodigingslink hieronder.')
            ->with('invite_link', $invitation->acceptUrl());
    }

    /** Change an existing member's role. */
    public function updateRole(Request $request, User $user)
    {
        $this->authorizeSameCompany($request, $user);

        $data = $request->validate([
            'role' => ['required', Rule::in(array_keys(Roles::assignableRoles()))],
        ]);

        if ($user->isOwner()) {
            return back()->with('error', 'De rol van de eigenaar kan niet worden gewijzigd.');
        }
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Je kunt je eigen rol niet wijzigen.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('success', $user->name . ' is nu ' . Roles::label($data['role']) . '.');
    }

    /** Remove a member from the company. */
    public function destroy(Request $request, User $user)
    {
        $this->authorizeSameCompany($request, $user);

        if ($user->isOwner()) {
            return back()->with('error', 'De eigenaar kan niet worden verwijderd.');
        }
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Je kunt jezelf niet verwijderen.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', $name . ' is uit het team verwijderd.');
    }

    /** Cancel a pending invitation. */
    public function cancelInvite(Request $request, TeamInvitation $invitation)
    {
        abort_unless($invitation->company_id === $request->user()->company_id, 403);

        $invitation->delete();

        return back()->with('success', 'Uitnodiging ingetrokken.');
    }

    private function authorizeSameCompany(Request $request, User $user): void
    {
        abort_unless($user->company_id === $request->user()->company_id, 403);
    }
}
