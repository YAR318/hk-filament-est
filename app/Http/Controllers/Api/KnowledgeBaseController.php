<?php

namespace App\Http\Controllers\Api;

use App\Models\KnowledgeDocument;
use Illuminate\Http\JsonResponse;

class KnowledgeBaseController
{
    /**
     * Obtener todo el contexto de la base de conocimiento
     *
     * GET /api/knowledge-base
     */
    public function index(): JsonResponse
    {
        $context = KnowledgeDocument::getFullContext();
        $count = KnowledgeDocument::active()->count();

        return response()->json([
            'success' => true,
            'context' => $context,
            'documents_count' => $count,
        ]);
    }
}
