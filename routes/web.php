<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\Admin\QuestionCategoryController;
/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/
Route::get('/', [AppointmentController::class, 'home'])->name('home');
Route::get('/about', [AppointmentController::class, 'about'])->name('about');

// ========== AUTH ==========
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES (auth requis)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
      Route::get('/doctor/{id}/profile', [DoctorProfileController::class, 'show'])
         ->name('doctor.profile');
    // ========== RENDEZ-VOUS ==========
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/search', [AppointmentController::class, 'searchDoctors'])->name('search');
        Route::get('/doctor/{doctorId}/availabilities', [AppointmentController::class, 'showAvailabilities'])->name('availabilities');
        Route::post('/book/{doctorId}', [AppointmentController::class, 'book'])->name('book');
        Route::post('/cancel/{appointmentId}', [AppointmentController::class, 'cancel'])->name('cancel');
        Route::get('/manage', [AppointmentController::class, 'manageAvailabilitiesForm'])->name('manage_availabilities');
        Route::post('/manage', [AppointmentController::class, 'manageAvailabilities'])->name('manage');
        Route::get('/today', [AppointmentController::class, 'todayAppointments'])->name('today');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        // PROPOSITION DE NOUVEAU RDV
        Route::get('/{appointmentId}/propose', [AppointmentController::class, 'proposeAlternative'])
             ->name('propose_alternative');
        
        Route::post('/{appointmentId}/send-alternative', [AppointmentController::class, 'sendAlternativeProposal'])
             ->name('send_alternative');
    });

    // ========== MESSAGERIE PRIVÉE ==========
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [PrivateMessageController::class, 'index'])->name('index');
        Route::get('/create', [PrivateMessageController::class, 'create'])->name('create');
        Route::post('/store', [PrivateMessageController::class, 'store'])->name('store');
        Route::get('/create-patient', [PrivateMessageController::class, 'createPatient'])->name('create-patient');
        Route::post('/store-patient', [PrivateMessageController::class, 'storePatient'])->name('store-patient');
        Route::get('/{messageId}/edit', [PrivateMessageController::class, 'edit'])->name('edit');
        Route::put('/{messageId}', [PrivateMessageController::class, 'update'])->name('update');
        Route::delete('/{messageId}', [PrivateMessageController::class, 'destroy'])->name('destroy');
        Route::delete('/conversation/{otherUserId}', [PrivateMessageController::class, 'destroyConversation'])->name('destroy-conversation');
        Route::get('/{otherUserId}', [PrivateMessageController::class, 'show'])->name('show');
        Route::post('/{receiverId}/send', [PrivateMessageController::class, 'send'])->name('send');
    });
// ========== FORUM – VERSION 100% CORRIGÉE ET TESTÉE ==========
Route::prefix('questions')->name('questions.')->middleware('auth')->group(function () {

    Route::get('/', [QuestionController::class, 'index'])->name('index');
    Route::get('/create', [QuestionController::class, 'create'])->name('create');
    Route::post('/', [QuestionController::class, 'store'])->name('store');
    Route::get('/my-questions', [QuestionController::class, 'myQuestions'])->name('my');

    Route::get('/{question}', [QuestionController::class, 'show'])->name('show');
    Route::get('/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
    Route::put('/{question}', [QuestionController::class, 'update'])->name('update');
    Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('destroy');
    Route::post('/{question}/answer', [QuestionController::class, 'answer'])->name('answer');

    // LA CORRECTION ULTIME : LE / DEVANT answers EST OBLIGATOIRE !
    Route::get('/answers/{answer}/edit', [QuestionController::class, 'editAnswer'])
         ->name('answers.edit');
    Route::put('/answers/{answer}', [QuestionController::class, 'updateAnswer'])
         ->name('answers.update');
    Route::delete('/answers/{answer}', [QuestionController::class, 'destroyAnswer'])
         ->name('answers.destroy');
});
    // ========== SUPPORT ADMIN ==========
    Route::prefix('admin-messages')->name('admin_messages.')->group(function () {
        Route::get('/', [AdminMessageController::class, 'index'])->name('index');
        Route::get('/create', [AdminMessageController::class, 'create'])->name('create');
        Route::post('/', [AdminMessageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminMessageController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminMessageController::class, 'update'])->name('update');
        Route::get('/{id}/edit-response', [AdminMessageController::class, 'editResponse'])->name('editResponse');
        Route::patch('/{id}/response', [AdminMessageController::class, 'updateResponse'])->name('updateResponse');
        Route::patch('/{id}/status', [AdminMessageController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [AdminMessageController::class, 'destroy'])->name('destroy');
        Route::delete('/{id}/response', [AdminMessageController::class, 'destroyResponse'])->name('destroyResponse');
    });
// NOTIFICATIONS – VERSION INVISIBLE DANS LE NAVIGATEUR (2025 FINAL)
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {

    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{id}', [NotificationController::class, 'show'])->name('show');

    // API CLOCHE – INVISIBLE DANS LE NAVIGATEUR
    Route::get('/api/unread', function () {
        // SI C'EST PAS UNE REQUÊTE AJAX → 404 DIRECT
        if (!request()->expectsJson() && !request()->ajax() && !request()->header('X-Requested-With')) {
            abort(404);
        }

        $user = auth()->user();

        $unreadCount = DB::table('notifications')
            ->where(function($q) use ($user) {
                $q->where('receiver_id', $user->id)
                  ->orWhere(function($q2) use ($user) {
                      $q2->whereNull('receiver_id')
                         ->where('receiver_specialty', $user->role === 'admin' ? 'admin' : ($user->specialty ?? $user->role));
                  });
            })
            ->where('read', false)
            ->count();

        $notifications = DB::table('notifications')
            ->where(function($q) use ($user) {
                $q->where('receiver_id', $user->id)
                  ->orWhere(function($q2) use ($user) {
                      $q2->whereNull('receiver_id')
                         ->where('receiver_specialty', $user->role === 'admin' ? 'admin' : ($user->specialty ?? $user->role));
                  });
            })
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(fn($n) => [
                'message' => Str::limit(strip_tags($n->message), 90, '...'),
                'time'    => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                'url'     => route('notifications.show', $n->id),
                'icon'    => 'fas fa-bell',
            ])
        ])->header('Content-Type', 'application/json');
    })->name('api.unread');

    // Marquer comme lues
    Route::post('/api/mark-all-read', function () {
        if (!request()->ajax()) abort(404);

        DB::table('notifications')
            ->where(function($q) {
                $user = auth()->user();
                $q->where('receiver_id', $user->id)
                  ->orWhere(function($q2) use ($user) {
                      $q2->whereNull('receiver_id')
                         ->where('receiver_specialty', $user->role === 'admin' ? 'admin' : ($user->specialty ?? $user->role));
                  });
            })
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    })->name('api.mark-all-read');
});
    // ========== PROFIL ==========
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========== ADMIN DASHBOARD ==========
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/doctors/{id}/approve', [AdminDashboardController::class, 'approveDoctor'])->name('approve');
    Route::post('/doctors/{id}/reject', [AdminDashboardController::class, 'rejectDoctor'])->name('reject');
    // ========== ADMIN CATEGORIES ==========
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [QuestionCategoryController::class, 'index'])->name('index');
    Route::get('/create', [QuestionCategoryController::class, 'create'])->name('create');
    Route::post('/', [QuestionCategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [QuestionCategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [QuestionCategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [QuestionCategoryController::class, 'destroy'])->name('destroy');
});
});
