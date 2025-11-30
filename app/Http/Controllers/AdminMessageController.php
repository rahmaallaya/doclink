<?php

namespace App\Http\Controllers;

use App\Models\AdminMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $messages = $user->role === 'admin'
            ? AdminMessage::with('user')->latest()->get()
            : AdminMessage::where('user_id', $user->id)->latest()->get();

        return view('admin_messages.index', compact('messages', 'user'));
    }

    public function create()
    {
        $users = Auth::user()->role === 'admin' ? User::orderBy('name')->get() : collect();
        return view('admin_messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'user_id'  => 'nullable|exists:users,id',
        ]);

        $user = Auth::user();

        // === Envoi à tous les utilisateurs (admin uniquement) ===
        if ($user->role === 'admin' && $request->user_id === 'all') {
            foreach (User::all() as $u) {
                AdminMessage::create([
                    'user_id'  => $u->id,
                    'subject'  => $request->subject,
                    'message'  => $request->message,
                    'priority' => $request->priority,
                    'status'   => 'open',
                ]);
            }
            return redirect()->route('admin_messages.index')->with('success', 'Message envoyé à tous les utilisateurs.');
        }

        // === Création du ticket normal ===
        $ticket = AdminMessage::create([
            'user_id'  => $user->role === 'admin' ? $request->user_id : $user->id,
            'subject'  => $request->subject,
            'message'  => $request->message,
            'priority' => $request->priority,
            'status'   => 'open',
        ]);

        // === NOTIFIER LES ADMINS (quand un utilisateur crée un ticket) ===
        if ($user->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                DB::table('notifications')->insert([
                    'user_id'         => $admin->id,
                    'receiver_id'     => $admin->id,
                    'type'            => 'support_ticket',
                    'related_id'      => $ticket->id,
                    'message'         => "Nouveau ticket de <strong>{$user->name}</strong> : {$ticket->subject}",
                    'read'            => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        return redirect()->route('admin_messages.index')->with('success', 'Votre ticket a été envoyé avec succès !');
    }

    public function updateResponse(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate(['admin_response' => 'required|string|max:2000']);

        $msg = AdminMessage::findOrFail($id);
        $msg->admin_response = $request->admin_response;
        $msg->status = 'resolved';
        $msg->resolved_at = now();
        $msg->save();

        // === NOTIFIER L'UTILISATEUR QUE L'ADMIN A RÉPONDU ===
        DB::table('notifications')->insert([
            'user_id'         => $msg->user->id,
            'receiver_id'     => $msg->user->id,
            'type'            => 'admin_reply',
            'related_id'      => $msg->id,
            'message'         => "L'administrateur a répondu à votre ticket : <strong>{$msg->subject}</strong>",
            'read'            => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return back()->with('success', 'Réponse envoyée ! L\'utilisateur a été notifié.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $msg = AdminMessage::findOrFail($id);
        $msg->status = $request->status;
        $msg->resolved_at = in_array($request->status, ['open', 'in_progress']) ? null : now();
        $msg->save();

        return back()->with('success', 'Statut mis à jour.');
    }

    public function editResponse($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $message = AdminMessage::findOrFail($id);
        return view('admin_messages.edit_response', compact('message'));
    }

    public function edit($id)
    {
        $message = AdminMessage::findOrFail($id);
        if ($message->user_id !== Auth::id()) abort(403);
        return view('admin_messages.edit', compact('message'));
    }

    public function update(Request $request, $id)
    {
        $message = AdminMessage::findOrFail($id);
        if ($message->user_id !== Auth::id()) abort(403);

        $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $message->update($request->only('subject', 'message', 'priority'));
        return redirect()->route('admin_messages.index')->with('success', 'Message modifié.');
    }

    public function destroy($id)
    {
        $message = AdminMessage::findOrFail($id);
        if ($message->user_id !== auth()->id()) abort(403);
        $message->delete();
        return back()->with('success', 'Ticket supprimé définitivement.');
    }

    public function destroyResponse($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $message = AdminMessage::findOrFail($id);
        $message->admin_response = null;
        $message->save();
        return back()->with('success', 'Réponse supprimée.');
    }
}