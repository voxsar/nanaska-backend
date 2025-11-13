<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreSeenDocument;
use App\Models\BusinessModel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessModelController extends Controller
{
    /**
     * List all business models
     */
    public function index()
    {
        $models = BusinessModel::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $models,
        ]);
    }

    /**
     * Get specific business model details
     */
    public function show($id)
    {
        $model = BusinessModel::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    /**
     * Apply business model to case study
     */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'business_model_id' => 'required|exists:business_models,id',
            'pre_seen_document_id' => 'nullable|exists:pre_seen_documents,id',
            'case_context' => 'nullable|string',
            'specific_questions' => 'nullable|string',
        ]);

        $businessModel = BusinessModel::findOrFail($data['business_model_id']);
        $preSeenDocument = isset($data['pre_seen_document_id']) 
            ? PreSeenDocument::find($data['pre_seen_document_id']) 
            : null;

        // Prepare payload for N8N
        $payload = [
            'business_model_id' => $businessModel->id,
            'business_model_name' => $businessModel->name,
            'analysis_prompt' => $businessModel->analysis_prompt,
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
                Log::error('BusinessModelController: failed sending analysis request', [
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
                'business_model' => $businessModel,
                'pre_seen_document' => $preSeenDocument,
                'case_context' => $data['case_context'] ?? null,
                'specific_questions' => $data['specific_questions'] ?? null,
            ],
            'n8n_responses' => $responses,
        ]);
    }
}
