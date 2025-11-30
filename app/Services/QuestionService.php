<?php

namespace App\Services;

use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Models\QuestionCategory;
use App\Models\User;

class QuestionService
{
    protected $repository;
    protected $notificationService;

    public function __construct(
        QuestionRepositoryInterface $repository,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

    public function getAllQuestions()
    {
        return $this->repository->getAllQuestions();
    }

    public function getQuestionById(int $questionId)
    {
        return $this->repository->getQuestionById($questionId);
    }

    public function createQuestion(int $patientId, int $categoryId, string $title, string $content)
    {
        $question = $this->repository->createQuestion([
            'patient_id' => $patientId,
            'category_id' => $categoryId,
            'title' => $title,
            'content' => $content,
            'status' => 'open',
        ]);

        $this->notificationService->notifyNewQuestionInSpecialty($question);

        return $question;
    }

    public function addAnswer(int $questionId, int $doctorId, string $content)
    {
        $answer = $this->repository->addAnswer([
            'question_id' => $questionId,
            'doctor_id' => $doctorId,
            'content' => $content,
        ]);

        $question = $this->repository->getQuestionById($questionId);
        $this->notificationService->notifyQuestionAnswered($question, $answer);

        return $answer;
    }

    public function getQuestionsByPatient(int $patientId)
    {
        return $this->repository->getQuestionsByPatient($patientId);
    }

    // LES 4 MÉTHODES QUI MANQUAIENT ET QUI BLOQUAIENT TOUT
    public function updateQuestion(int $questionId, array $data)
    {
        return $this->repository->updateQuestion($questionId, $data);
    }

    public function deleteQuestion(int $questionId)
    {
        return $this->repository->deleteQuestion($questionId);
    }

    public function updateAnswer(int $answerId, array $data)
    {
        return $this->repository->updateAnswer($answerId, $data);
    }

    public function deleteAnswer(int $answerId)
    {
        return $this->repository->deleteAnswer($answerId);
    }
}