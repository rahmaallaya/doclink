<?php

namespace App\Repositories\Interfaces;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Collection;

interface QuestionRepositoryInterface
{
    public function getAllQuestions(): Collection;
    public function getQuestionsByCategory(int $categoryId): Collection;
    public function getQuestionById(int $questionId): Question;
    public function createQuestion(array $data): Question;
    public function addAnswer(array $data): Answer;
    public function getQuestionsByPatient(int $patientId): Collection;
    public function getOpenQuestions(): Collection;
}