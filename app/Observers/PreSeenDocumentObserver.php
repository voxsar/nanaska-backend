<?php

namespace App\Observers;

use App\Models\PreSeenDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PreSeenDocumentObserver
{
    /**
     * Handle the PreSeenDocument "created" event.
     */
    public function created(PreSeenDocument $document): void
    {
        // Send the uploaded pre-seen to both webhook endpoints with multipart key 'file_data'
        $fileAbsolute = storage_path('app/public/'.ltrim($document->file_path, '/'));

        if (! is_file($fileAbsolute)) {
            Log::warning('PreSeenDocumentObserver: file not found', [
                'file_path' => $document->file_path,
                'absolute' => $fileAbsolute,
                'id' => $document->id,
            ]);

            return;
        }

        $client = new Client([
            'timeout' => 20,
            'http_errors' => false,
        ]);

        $endpoints = [
            config('services.n8n.upload_url'),
            config('services.n8n.upload_test_url'),
        ];

        foreach ($endpoints as $url) {
            try {
                $client->post($url, [
                    'multipart' => [
                        [
                            'name' => 'file_data',
                            'contents' => fopen($fileAbsolute, 'r'),
                            'filename' => basename($fileAbsolute),
                        ],
                        [
                            'name' => 'meta',
                            'contents' => json_encode([
                                'pre_seen_document_id' => $document->id,
                                'name' => $document->name,
                                'company_name' => $document->company_name,
                                'description' => $document->description,
                            ]),
                        ],
                    ],
                ]);
            } catch (\Throwable $e) {
                Log::error('PreSeenDocumentObserver: failed to POST pre-seen document', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    // update
    public function updated(PreSeenDocument $document): void
    {
        // You can implement logic for the "updated" event if needed
        Log::info("running updated observer for pre-seen document ID: {$document->id}");

        // Send the uploaded pre-seen to both webhook endpoints with multipart key 'file_data'
        $fileAbsolute = storage_path('app/public/'.ltrim($document->file_path, '/'));

        if (! is_file($fileAbsolute)) {
            Log::warning('PreSeenDocumentObserver: file not found', [
                'file_path' => $document->file_path,
                'absolute' => $fileAbsolute,
                'id' => $document->id,
            ]);

            return;
        }

        $client = new Client([
            'timeout' => 20,
            'http_errors' => false,
        ]);

        $endpoints = [
            'https://automation.artslabcreatives.com/webhook-test/5a20a8b2-6853-4fac-b040-1fea5fca974e',
            'https://automation.artslabcreatives.com/webhook/5a20a8b2-6853-4fac-b040-1fea5fca974e',
        ];

        foreach ($endpoints as $url) {
            try {
                $client->post($url, [
                    'multipart' => [
                        [
                            'name' => 'file_data',
                            'contents' => fopen($fileAbsolute, 'r'),
                            'filename' => basename($fileAbsolute),
                        ],
                        [
                            'name' => 'meta',
                            'contents' => json_encode([
                                'pre_seen_document_id' => $document->id,
                                'name' => $document->name,
                                'company_name' => $document->company_name,
                                'description' => $document->description,
                            ]),
                        ],
                    ],
                ]);
            } catch (\Throwable $e) {
                Log::error('PreSeenDocumentObserver: failed to POST pre-seen document', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
