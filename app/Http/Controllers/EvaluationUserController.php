<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\EvaluationUserRepository;

class EvaluationUserController extends Controller
{
    protected $evaluationUserRepository;

    public function __construct(EvaluationUserRepository $evaluationUserRepository)
    {
        $this->evaluationUserRepository = $evaluationUserRepository;
    }

    public function getEvaluationGroupbyUser(Request $request)
    {
        return $this->evaluationUserRepository->getEvaluationGroupbyUser($request);
    }

    public function createEvaluteUserAPP(Request $request)
    {
        return $this->evaluationUserRepository->createEvaluteUserAPP($request);
    }

    public function getUsersEvaluationAPP(Request $request)
    {
        return $this->evaluationUserRepository->getUsersEvaluationAPP($request);
    }
}
