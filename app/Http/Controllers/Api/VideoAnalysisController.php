<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoAnalysisController extends Controller
{
    /**
     * Endpoint para que n8n reporte el estado y los resultados del analizador de videos
     */
    public function updateFromN8n(Request $request)
    {
        try {
            $validated = $request->validate([
                'analysis_id' => 'required|integer',
                'status' => 'required|string',
                'transcription' => 'nullable|string',
                'summary' => 'nullable|string',
                'key_decisions' => 'nullable|array',
                'tasks' => 'nullable|array',
                'error_message' => 'nullable|string',
            ]);

            $analysis = VideoAnalysis::findOrFail($validated['analysis_id']);

            $updateData = ['status' => $validated['status']];

            if ($request->has('transcription')) {
                $updateData['transcription'] = $validated['transcription'];
            }
            if ($request->has('summary')) {
                $updateData['summary'] = $validated['summary'];
            }
            if ($request->has('key_decisions')) {
                $updateData['key_decisions'] = $validated['key_decisions'];
            }
            if ($request->has('tasks')) {
                $updateData['tasks'] = $validated['tasks'];
            }
            if ($request->has('error_message')) {
                $updateData['error_message'] = $validated['error_message'];
            }

            $analysis->update($updateData);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Endpoint updateFromN8n fallo', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
