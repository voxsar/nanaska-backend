<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreSeenDocument;
use App\Models\TheoryModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TheoryModelController extends Controller
{
    /**
     * List all theory models
     */
    public function index()
    {
        $models = TheoryModel::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $models,
        ]);
    }

    /**
     * Get specific theory model details
     */
    public function show($id)
    {
        $model = TheoryModel::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    /**
     * Apply theory model to case study
     */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'theory_model_id' => 'required|exists:theory_models,id',
            'pre_seen_document_id' => 'nullable|exists:pre_seen_documents,id',
            'case_context' => 'nullable|string',
            'specific_questions' => 'nullable|string',
        ]);

        $theoryModel = TheoryModel::findOrFail($data['theory_model_id']);
        $preSeenDocument = isset($data['pre_seen_document_id']) 
            ? PreSeenDocument::find($data['pre_seen_document_id']) 
            : null;

        // Prepare payload for N8N
        $payload = [
            'theory_model_id' => $theoryModel->id,
            'theory_model_name' => $theoryModel->name,
            'analysis_prompt' => $theoryModel->analysis_prompt,
            'case_context' => $data['case_context'] ?? '',
            'specific_questions' => $data['specific_questions'] ?? '',
            'pre_seen_document' => $preSeenDocument ? [
                'id' => $preSeenDocument->id,
                'name' => $preSeenDocument->name,
                'file_path' => $preSeenDocument->file_path,
            ] : null,
        ];

        $client = new Client(['timeout' => 30, 'http_errors' => false]);

        // Send to both test and production endpoints
        $endpoints = [
            config('services.n8n.analysis_test_model_url'),
            config('services.n8n.analysis_model_url'),
        ];

        $responses = [];
        foreach ($endpoints as $url) {
            if (!$url) {
                continue;
            }

            try {
                $response = $client->post($url, [
                    'json' => $payload,
                ]);

                $responses[] = [
                    'url' => $url,
                    'status' => $response->getStatusCode(),
                    'body' => json_decode($response->getBody()->getContents(), true),
                ];
            } catch (\Throwable $e) {
                Log::error('TheoryModelController: failed sending analysis request', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
                
                $responses[] = [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Analysis request submitted to N8N',
            'data' => [
                'theory_model' => $theoryModel,
                'pre_seen_document' => $preSeenDocument,
                'case_context' => $data['case_context'] ?? null,
                'specific_questions' => $data['specific_questions'] ?? null,
            ],
            'n8n_responses' => $responses,
        ]);
    }
}
