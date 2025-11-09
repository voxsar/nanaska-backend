<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreSeenDocument;
use Illuminate\Http\Request;

class PreSeenDocumentController extends Controller
{
    /**
     * List all pre-seen documents
     */
    public function index()
    {
        $documents = PreSeenDocument::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Get specific pre-seen document details
     */
    public function show($id)
    {
        $document = PreSeenDocument::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $document,
        ]);
    }
}
