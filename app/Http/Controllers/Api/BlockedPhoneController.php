<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockedPhone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlockedPhoneController extends Controller
{
    /**
     * Verificar si un número está bloqueado
     * 
     * @param string $phone
     * @return JsonResponse
     */
    public function check(string $phone): JsonResponse
    {
        // Normalizar el número eliminando caracteres no numéricos
        $normalizedPhone = BlockedPhone::normalizePhone($phone);

        $isBlocked = BlockedPhone::isBlocked($normalizedPhone);

        return response()->json([
            'phone' => $normalizedPhone,
            'blocked' => $isBlocked,
            'reason' => $isBlocked ?BlockedPhone::where('phone_number', $normalizedPhone)->value('reason') : null,
        ]);
    }
}