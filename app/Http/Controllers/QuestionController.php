<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\QuestionCategory;
use Illuminate\Support\Str;           
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
        $this->middleware('auth');
    }

    // PAGE PRINCIPALE DU FORUM
    public function index()
    {
        $questions = Question::with(['patient:id,name', 'category:id,name'])
            ->select('id', 'patient_id', 'category_id', 'title', 'status', 'created_at', 'updated_at')
            ->latest()
            ->paginate(12);

        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'patient') abort(403);
        $categories = QuestionCategory::select('id', 'name')->get();
        return view('questions.create', compact('categories'));
    }

    public function store(Request $request)
{
    if (Auth::user()->role !== 'patient') abort(403);

    $request->validate([
        'category_id' => 'required|exists:question_categories,id',
        'title'       => 'required|string|max:255',
        'content'     => 'required|string|min:20',
    ]);

    $question = $this->questionService->createQuestion(
        Auth::id(),
        $request->category_id,
        $request->title,
        $request->content
    );

    // Récupérer le nom de la spécialité
    $category = \App\Models\QuestionCategory::find($request->category_id);

    // NOTIFICATION À TOUS LES MÉDECINS DE LA SPÉCIALITÉ
    app(\App\Services\NotificationService::class)->create(
        senderId: Auth::id(),
        receiverId: null,
        receiverSpecialty: $category->name,
        type: 'new_question',
        relatedId: $question->id,
        message: "Nouvelle question en <strong>{$category->name}</strong> :<br>" .
                 "<em>\"" . Str::limit($request->title, 80) . "\"</em>"
    );

    return redirect()->route('questions.index')->with('success', 'Question posée avec succès !');
}
    public function show(Question $question)
    {
        $question->load([
            'patient:id,name',
            'category:id,name',
            'answers.doctor:id,name,specialty'
        ]);

        $canAnswer = Auth::user()->role === 'medecin' &&
                     Auth::user()->specialty === $question->category->name;

        return view('questions.show', compact('question', 'canAnswer'));
    }

    public function edit(Question $question)
    {
        if ($question->patient_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $categories = QuestionCategory::select('id', 'name')->get();
        return view('questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        if ($question->patient_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'category_id' => 'required|exists:question_categories,id',
            'title'       => 'required|string|max:255',
            'content'     => 'required|string|min:20',
        ]);

        $this->questionService->updateQuestion($question->id, [
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'content'     => $request->content,
        ]);

        return redirect()->route('questions.show', $question)
            ->with('success', 'Question modifiée avec succès !');
    }

    public function destroy(Question $question)
    {
        if ($question->patient_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $this->questionService->deleteQuestion($question->id);

        return redirect()->route('questions.index')
            ->with('success', 'Question supprimée.');
    }

    public function answer(Request $request, Question $question)
{
    if (Auth::user()->role !== 'medecin' || Auth::user()->specialty !== $question->category->name) {
        return back()->with('error', 'Seuls les médecins de cette spécialité peuvent répondre.');
    }

    $request->validate(['content' => 'required|string|min:20']);

    $answer = $this->questionService->addAnswer($question->id, Auth::id(), $request->content);

    // NOTIFICATION AU PATIENT QUI A POSÉ LA QUESTION
    app(\App\Services\NotificationService::class)->create(
        senderId: Auth::id(),
        receiverId: $question->patient_id,
        receiverSpecialty: null,
        type: 'question_answered',
        relatedId: $question->id,
        message: "Le Dr <strong>" . Auth::user()->name . "</strong> a répondu à votre question :<br>" .
                 "<em>\"" . Str::limit($question->title, 70) . "\"</em>"
    );

    return back()->with('success', 'Réponse publiée !');
}

    public function myQuestions()
    {
        $questions = $this->questionService->getQuestionsByPatient(Auth::id());
        return view('questions.my_questions', compact('questions'));
    }

    // Réponses médecins
    public function editAnswer(Answer $answer)
    {
        if ($answer->doctor_id !== Auth::id()) abort(403);
        return view('questions.edit_answer', compact('answer'));
    }

    public function updateAnswer(Request $request, Answer $answer)
    {
        if ($answer->doctor_id !== Auth::id()) abort(403);
       
        $this->questionService->updateAnswer($answer->id, ['content' => $request->content]);
        return redirect()->route('questions.show', $answer->question_id)->with('success', 'Réponse modifiée !');
    }

    public function destroyAnswer(Answer $answer)
    {
        if ($answer->doctor_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        $this->questionService->deleteAnswer($answer->id);
        return redirect()->route('questions.show', $answer->question_id)->with('success', 'Réponse supprimée.');
    }
}