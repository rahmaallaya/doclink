<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Availability;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $notificationService;

    public function __construct(AppointmentService $appointmentService, NotificationService $notificationService)
    {
        $this->appointmentService = $appointmentService;
        $this->notificationService = $notificationService;
        $this->middleware('auth')->except(['home', 'about']);
    }

    // === PAGES PUBLIQUES ===
    public function home()
    {
        if (Auth::check()) {
            return match (Auth::user()->role) {
                'patient' => redirect()->route('appointments.index'),
                'medecin' => redirect()->route('appointments.today'),
                'admin'   => redirect()->route('admin.dashboard'),
                default  => redirect()->route('appointments.index'),
            };
        }
        return view('home');
    }

    public function about() { return view('about'); }

    public function index()
    {
        $user = Auth::user();
        $history = $user->role === 'medecin'
            ? $this->appointmentService->getAgenda($user->id)
            : $this->appointmentService->getAppointments($user->id, $user->role);

        return view('appointments.index', compact('user', 'history'));
    }

    public function searchDoctors(Request $request)
    {
        $user = Auth::user();
        $query = User::where('role', 'medecin')->where('status', 'ACTIVE');

        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', "%{$request->specialty}%");
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $doctors = $query->get();
        return view('appointments.search', compact('doctors', 'user'));
    }

    public function showAvailabilities($doctorId)
    {
        $this->authorizeRole('patient');
        $doctor = User::where('role', 'medecin')->where('status', 'ACTIVE')->findOrFail($doctorId);
        $availableSlots = $this->appointmentService->getAvailableSlots($doctorId);

        return view('appointments.availabilities', compact('availableSlots', 'doctorId', 'doctor'));
    }

   // Dans book() → après la création du RDV
public function book(Request $request, $doctorId)
{
    $this->authorizeRole('patient');

    $request->validate([
        'availability_id' => 'required|exists:availabilities,id',
        'start_time'      => 'required|date',
        'end_time'        => 'required|date|after:start_time',
    ]);

    // VÉRIFICATION AMÉLIORÉE : s'assurer que le créneau n'est pas déjà réservé
    $alreadyBooked = Appointment::where('availability_id', $request->availability_id)
                                ->where('status', 'planned') // Seulement les RDV actifs
                                ->exists();

    if ($alreadyBooked) {
        return back()->with('error', 'Désolé, ce créneau a déjà été réservé par un autre patient !');
    }

    // VÉRIFICATION SUPPLÉMENTAIRE : s'assurer que le créneau existe et appartient au médecin
    $availability = Availability::where('id', $request->availability_id)
                                ->where('doctor_id', $doctorId)
                                ->first();

    if (!$availability) {
        return back()->with('error', 'Créneau de disponibilité non trouvé.');
    }

    // VÉRIFIER QUE LE CRÉNEAU N'EST PAS DANS LE PASSÉ
    if (Carbon::parse($request->start_time)->isPast()) {
        return back()->with('error', 'Impossible de réserver un créneau dans le passé.');
    }

    $doctor = User::where('id', $doctorId)->where('role', 'medecin')->where('status', 'ACTIVE')->firstOrFail();

    $appointment = Appointment::create([
        'user_id'         => Auth::id(),
        'doctor_id'       => $doctorId,
        'availability_id' => $request->availability_id,
        'appointment_time' => $request->start_time,
        'status'          => 'planned',
    ]);

    // NOTIFICATION AU MÉDECIN
    $this->notificationService->create(
        senderId: Auth::id(),
        receiverId: $doctorId,
        receiverSpecialty: null,
        type: 'appointment_booked',
        relatedId: $appointment->id,
        message: "Nouveau rendez-vous avec <strong>" . Auth::user()->name . "</strong> le " .
                 Carbon::parse($appointment->appointment_time)->format('d/m/Y à H:i')
    );

    return redirect()->route('appointments.index')
                     ->with('success', 'Rendez-vous pris avec succès !');
}

    // Dans cancel() → après l'annulation
// Dans app/Http/Controllers/AppointmentController.php

public function cancel($appointmentId)
{
    $appointment = Appointment::findOrFail($appointmentId);

    if ($appointment->user_id !== Auth::id() && $appointment->doctor_id !== Auth::id()) {
        abort(403);
    }

    // SI C'EST LE MÉDECIN QUI ANNULE → Rediriger vers formulaire de proposition
    if (Auth::id() === $appointment->doctor_id) {
        return redirect()->route('appointments.propose_alternative', $appointment->id);
    }

    // Sinon annulation normale
    $this->appointmentService->cancelAppointment($appointmentId);

    if (Auth::id() === $appointment->user_id) {
        $this->notificationService->create(
            senderId: Auth::id(),
            receiverId: $appointment->doctor_id,
            receiverSpecialty: null,
            type: 'appointment_cancelled_by_patient',
            relatedId: $appointment->id,
            message: "Annulation : <strong>" . Auth::user()->name . "</strong> a annulé le RDV du " .
                     Carbon::parse($appointment->appointment_time)->format('d/m/Y à H:i')
        );
    }

    return redirect()->route('appointments.index')->with('success', 'Rendez-vous annulé.');
}

public function proposeAlternative($appointmentId)
{
    $appointment = Appointment::with(['user', 'doctor'])->findOrFail($appointmentId);
    
    // Sécurité : seul le médecin concerné
    if ($appointment->doctor_id !== Auth::id()) {
        abort(403);
    }

    // Récupérer les disponibilités futures du médecin
    $availableSlots = $this->appointmentService->getAvailableSlots(Auth::id());

    return view('appointments.propose_alternative', compact('appointment', 'availableSlots'));
}

public function sendAlternativeProposal(Request $request, $appointmentId)
{
    $request->validate([
        'availability_id' => 'required|exists:availabilities,id',
        'message' => 'nullable|string|max:500'
    ]);

    $appointment = Appointment::findOrFail($appointmentId);
    
    if ($appointment->doctor_id !== Auth::id()) {
        abort(403);
    }

    // Annuler l'ancien RDV
    $appointment->update(['status' => 'cancelled']);

    // Notification au patient avec proposition
    $newSlot = Availability::findOrFail($request->availability_id);
    
    $this->notificationService->create(
        senderId: Auth::id(),
        receiverId: $appointment->user_id,
        receiverSpecialty: null,
        type: 'appointment_rescheduled',
        relatedId: $appointment->id,
        message: "Le Dr <strong>" . Auth::user()->name . "</strong> vous propose un nouveau créneau :<br>" .
                 "<strong>" . Carbon::parse($newSlot->start_time)->format('d/m/Y à H:i') . "</strong><br>" .
                 ($request->message ? "<em>Message : {$request->message}</em>" : "")
    );

    return redirect()->route('appointments.today')
                     ->with('success', 'Proposition envoyée au patient.');
}

    public function todayAppointments()
    {
        $this->authorizeRole('medecin');
        $todayAgenda = $this->appointmentService->getTodayAgenda(Auth::id());
        return view('appointments.today', compact('todayAgenda'));
    }

   public function manageAvailabilitiesForm()
{
    $this->authorizeRole('medecin');

    // CHARGE LA RELATION appointment → INDISPENSABLE !
   $availabilities = Availability::with('appointment')
    ->where('doctor_id', Auth::id())
    ->orderBy('start_time')
    ->paginate(12); 

    return view('appointments.manage_availabilities', compact('availabilities'));
}

public function manageAvailabilities(Request $request)
{
    $this->authorizeRole('medecin');

    try {
        if ($request->action === 'add') {
            $start = Carbon::parse($request->start_time);

            if ($start->isPast()) {
                return response()->json(['success' => false, 'message' => 'Impossible : date dans le passé'], 400);
            }

            $conflict = Availability::where('doctor_id', Auth::id())
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhereRaw('? BETWEEN start_time AND end_time', [$request->start_time]);
                })->exists();

            if ($conflict) {
                return response()->json(['success' => false, 'message' => 'Conflit avec un autre créneau'], 400);
            }

            Availability::create([
                'doctor_id'  => Auth::id(),
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]);

            return response()->json(['success' => true, 'message' => 'Créneau ajouté avec succès']);

        } elseif ($request->action === 'delete') {
            $avail = Availability::where('id', $request->availability_id)
                                  ->where('doctor_id', Auth::id())
                                  ->firstOrFail();

            if ($avail->appointment) {
                return response()->json(['success' => false, 'message' => 'Créneau réservé → impossible de supprimer'], 400);
            }

            $avail->delete();
            return response()->json(['success' => true, 'message' => 'Créneau supprimé avec succès']);
        }

    } catch (\Exception $e) {
        Log::error('Erreur gestion disponibilités: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
    }
}

    private function authorizeRole($roles)
    {
        if (!is_array($roles)) $roles = [$roles];
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Accès refusé.');
        }
    }

public function show(Appointment $appointment)
{
    // Sécurité : seul le patient ou le médecin concerné peut voir
    if (auth()->id() !== $appointment->user_id && auth()->id() !== $appointment->doctor_id) {
        abort(403, 'Accès refusé.');
    }

    return view('appointments.show', compact('appointment'));
}

}