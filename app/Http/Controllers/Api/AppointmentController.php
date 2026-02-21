<?php

namespace App\Http\Controllers\Api;

use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * Crear una nueva cita
     *
     * POST /api/appointments/create
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:120',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $appointment = $this->appointmentService->create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cita creada exitosamente',
                'data' => [
                    'id' => $appointment->id,
                    'client_name' => $appointment->client_name,
                    'scheduled_at' => $appointment->scheduled_at->format('d/m/Y H:i'),
                    'meet_link' => $appointment->meet_link,
                    'status' => $appointment->status,
                ],
            ]);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
        catch (\Exception $e) {
            Log::error('Error creando cita', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al crear la cita',
            ], 500);
        }
    }

    /**
     * Cancelar una cita
     *
     * POST /api/appointments/cancel
     */
    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required_without:client_phone|integer|exists:appointments,id',
            'client_phone' => 'required_without:appointment_id|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Si viene por teléfono, buscar la próxima cita
            if (!$request->appointment_id && $request->client_phone) {
                $nextAppointment = $this->appointmentService->getNextAppointmentByPhone($request->client_phone);
                if (!$nextAppointment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró ninguna cita próxima para este número.',
                    ], 404);
                }
                $appointmentId = $nextAppointment->id;
            }
            else {
                $appointmentId = $request->appointment_id;
            }

            $appointment = $this->appointmentService->cancel($appointmentId, $request->client_phone);

            return response()->json([
                'success' => true,
                'message' => 'Cita cancelada exitosamente',
                'data' => [
                    'id' => $appointment->id,
                    'scheduled_at' => $appointment->scheduled_at->format('d/m/Y H:i'),
                    'status' => $appointment->status,
                ],
            ]);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
        catch (\Exception $e) {
            Log::error('Error cancelando cita', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al cancelar la cita',
            ], 500);
        }
    }

    /**
     * Reagendar una cita
     *
     * POST /api/appointments/reschedule
     */
    public function reschedule(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required_without:client_phone|integer|exists:appointments,id',
            'client_phone' => 'required_without:appointment_id|string',
            'new_scheduled_at' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (!$request->appointment_id && $request->client_phone) {
                $nextAppointment = $this->appointmentService->getNextAppointmentByPhone($request->client_phone);
                if (!$nextAppointment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró ninguna cita próxima para este número.',
                    ], 404);
                }
                $appointmentId = $nextAppointment->id;
            }
            else {
                $appointmentId = $request->appointment_id;
            }

            $appointment = $this->appointmentService->reschedule(
                $appointmentId,
                $request->new_scheduled_at,
                $request->client_phone
            );

            return response()->json([
                'success' => true,
                'message' => 'Cita reagendada exitosamente',
                'data' => [
                    'id' => $appointment->id,
                    'scheduled_at' => $appointment->scheduled_at->format('d/m/Y H:i'),
                    'meet_link' => $appointment->meet_link,
                    'status' => $appointment->status,
                ],
            ]);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
        catch (\Exception $e) {
            Log::error('Error reagendando cita', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno al reagendar la cita',
            ], 500);
        }
    }

    /**
     * Consultar horarios disponibles
     *
     * GET /api/appointments/available/{date}
     */
    public function available(string $date): JsonResponse
    {
        try {
            $dateCarbon = Carbon::parse($date);

            if ($dateCarbon->isPast() && !$dateCarbon->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden consultar fechas pasadas.',
                ], 400);
            }

            $slots = $this->appointmentService->getAvailableSlots($dateCarbon);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $dateCarbon->format('d/m/Y'),
                    'day_name' => $dateCarbon->translatedFormat('l'),
                    'available_slots' => $slots,
                    'total_available' => count($slots),
                ],
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha inválida.',
            ], 400);
        }
    }
}