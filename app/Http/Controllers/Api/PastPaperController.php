<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use Illuminate\Http\Request;

class PastPaperController extends Controller
{
    public function index()
    {
        $pastPapers = PastPaper::with(['questions', 'questionPaper', 'answerGuide', 'markingGuide'])
            ->orderBy('year', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pastPapers,
        ]);
    }

    public function show($id)
    {
        $pastPaper = PastPaper::with(['questions', 'questionPaper', 'answerGuide', 'markingGuide'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pastPaper,
        ]);
    }

    public function questions($id)
    {
        $pastPaper = PastPaper::findOrFail($id);
        $questions = $pastPaper->questions()->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'past_paper' => $pastPaper,
                'questions' => $questions,
            ],
        ]);
    }
}
