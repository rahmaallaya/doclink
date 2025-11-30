<?php

namespace App\Http\Controllers;

use App\Services\PrivateMessageService;
use App\Models\User;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateMessageController extends Controller
{
    protected $service;

    public function __construct(PrivateMessageService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

   public function index()
{
    $user = Auth::user();

    $conversations = PrivateMessage::where('sender_id', $user->id)
                                   ->orWhere('receiver_id', $user->id)
                                   ->with(['sender', 'receiver'])
                                   ->latest()
                                   ->get()
                                   ->unique(function ($item) {
                                       return $item->sender_id < $item->receiver_id
                                           ? $item->sender_id . '-' . $item->receiver_id
                                           : $item->receiver_id . '-' . $item->sender_id;
                                   });

    $unreadCount = PrivateMessage::where('receiver_id', $user->id)
                                 ->where('read', false)
                                 ->count();

    return view('messages.index', compact('user', 'conversations', 'unreadCount'));
}

  public function show($otherUserId)
{
    $messages = $this->service->getConversation(Auth::id(), $otherUserId);

    // Marque toute la conversation comme lue
    $this->service->markConversationAsRead(Auth::id(), $otherUserId);

    $otherUser = User::findOrFail($otherUserId);

    return view('messages.show', [
        'messages'  => $messages,
        'otherUser' => $otherUser,
        'userId'    => Auth::id(),   // Obligatoire pour la vue
    ]);
}

    public function send(Request $request, $receiverId)
{
    $request->validate(['message' => 'required|string']);

    // Envoi du message
    $this->service->sendMessage(
        senderId: Auth::id(),
        receiverId: $receiverId,
        subject: $request->subject ?? null,
        message: $request->message
    );

    // NOTIFICATION EN TEMPS RÉEL AU DESTINATAIRE
    $receiver = User::findOrFail($receiverId);
    $senderName = Auth::user()->name;

    app(\App\Services\NotificationService::class)->create(
        senderId: Auth::id(),
        receiverId: $receiverId,
        receiverSpecialty: null,
        type: 'private_message',
        relatedId: $receiverId, // on met l'ID du destinataire pour pouvoir faire un lien vers la conversation
        message: "Nouveau message privé de <strong>{$senderName}</strong>"
    );

    return back()->with('success', 'Message envoyé');
}
    public function create()
    {
        if (Auth::user()->role !== 'medecin') abort(403);
        $patients = User::where('role', 'patient')->get();
        return view('messages.create', compact('patients'));
    }

public function createPatient()
{
    if (Auth::user()->role !== 'patient') abort(403);
    $doctors = User::where('role', 'medecin')->get();
    $selectedDoctorId = request('doctor_id');  // Récupère de l'URL
    return view('messages.create_patient', compact('doctors', 'selectedDoctorId'));
}
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'medecin') abort(403);
        $request->validate(['receiver_id' => 'required|exists:users,id', 'message' => 'required']);
        $this->service->sendMessage(Auth::id(), $request->receiver_id, $request->subject, $request->message);
        return redirect()->route('messages.index')->with('success', 'Message envoyé');
        $createdMessage = AdminMessage::create([
        'user_id'  => $user->role === 'admin' ? $request->user_id : $user->id,
        'subject'  => $request->subject,
        'message'  => $request->message,
        'priority' => $request->priority,
        'status'   => 'open',
    ]);

    // NOTIFICATION À L'ADMIN (si c'est un utilisateur qui envoie)
    if ($user->role !== 'admin') {
        app(\App\Services\NotificationService::class)->create(
            senderId: $user->id,
            receiverId: null, // on cible tous les admins (ou tu peux cibler un admin spécifique)
            receiverSpecialty: 'admin', // on utilise un champ spécial pour cibler les admins
            type: 'admin_message_received',
            relatedId: $createdMessage->id,
            message: "Nouveau ticket support de <strong>{$user->name}</strong> : {$request->subject}"
        );
    }

    return redirect()->route('admin_messages.index')->with('success', 'Message envoyé.');
}
    

    public function storePatient(Request $request)
    {
        if (Auth::user()->role !== 'patient') abort(403);
        $request->validate(['receiver_id' => 'required|exists:users,id', 'message' => 'required']);
        $this->service->sendMessage(Auth::id(), $request->receiver_id, $request->subject, $request->message);
        return redirect()->route('messages.index')->with('success', 'Message envoyé');
    }

    public function edit($id)
    {
        $msg = PrivateMessage::findOrFail($id);
        if ($msg->sender_id !== Auth::id()) abort(403);
        return view('messages.edit', compact('msg'));
    }

    public function update(Request $request, $id)
    {
        $msg = PrivateMessage::findOrFail($id);
        if ($msg->sender_id !== Auth::id()) abort(403);
        $request->validate(['message' => 'required|string']);
        $msg->update($request->only('subject', 'message'));
        return redirect()->route('messages.show', $msg->receiver_id)->with('success', 'Modifié');
    }

    public function destroy($id)
    {
        $msg = PrivateMessage::findOrFail($id);
        if ($msg->sender_id !== Auth::id() && $msg->receiver_id !== Auth::id()) abort(403);
        $redirectId = $msg->sender_id === Auth::id() ? $msg->receiver_id : $msg->sender_id;
        $msg->delete();
        return redirect()->route('messages.show', $redirectId)->with('success', 'Supprimé');
    }

    public function destroyConversation($otherUserId)
    {
        PrivateMessage::where(function($q) use ($otherUserId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $otherUserId);
        })->orWhere(function($q) use ($otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', Auth::id());
        })->delete();

        return redirect()->route('messages.index')->with('success', 'Conversation supprimée');
    }
}