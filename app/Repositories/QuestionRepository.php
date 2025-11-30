<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Collection;

class QuestionRepository implements \App\Repositories\Interfaces\QuestionRepositoryInterface
{
    public function getAllQuestions(): Collection
    {
        return Question::with(['patient:id,name', 'category:id,name'])
            ->select('id', 'patient_id', 'category_id', 'title', 'status', 'created_at')
            ->latest()
            ->get();
    }

    public function getQuestionsByCategory(int $categoryId): Collection
    {
        return Question::with(['patient:id,name', 'category:id,name'])
            ->where('category_id', $categoryId)
            ->select('id', 'patient_id', 'category_id', 'title', 'status', 'created_at')
            ->latest()
            ->get();
    }

    public function getOpenQuestions(): Collection
    {
        return Question::with(['patient:id,name', 'category:id,name'])
            ->where('status', 'open')
            ->select('id', 'patient_id', 'category_id', 'title', 'created_at')
            ->latest()
            ->get();
    }

    public function getQuestionById(int $questionId): Question
    {
        return Question::with([
                'patient:id,name',
                'category:id,name',
                'answers.doctor:id,name,specialty'
            ])
            ->findOrFail($questionId);
    }

    public function createQuestion(array $data): Question
    {
        return Question::create($data);
    }

    public function addAnswer(array $data): Answer
    {
        $answer = Answer::create($data);
        Question::where('id', $data['question_id'])->update(['status' => 'answered']);
        return $answer;
    }

    public function getQuestionsByPatient(int $patientId): Collection
    {
        return Question::with('category:id,name')
            ->where('patient_id', $patientId)
            ->select('id', 'category_id', 'title', 'status', 'created_at')
            ->latest()
            ->get();
    }

    // CES 4 MÉTHODES MANQUAIENT → C’EST ÇA QUI BLOQUAIT TOUT !
    public function updateQuestion(int $questionId, array $data): Question
    {
        $question = Question::findOrFail($questionId);
        $question->update($data);
        return $question->fresh();
    }

    public function deleteQuestion(int $questionId): bool
    {
        return Question::where('id', $questionId)->delete();
    }

    public function updateAnswer(int $answerId, array $data): Answer
    {
        $answer = Answer::findOrFail($answerId);
        $answer->update($data);
        return $answer;
    }

    public function deleteAnswer(int $answerId): bool
    {
        return Answer::where('id', $answerId)->delete();
    }
}