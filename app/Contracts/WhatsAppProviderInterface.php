<?php

namespace App\Contracts;

/**
 * Interfaz unificada para proveedores de WhatsApp.
 * Tanto Evolution como Meta implementan este contrato.
 */
interface WhatsAppProviderInterface
{
    /**
     * Enviar un mensaje de texto simple
     */
    public function sendMessage(string $phoneNumber, string $text): array;

    /**
     * Obtener el nombre del proveedor
     */
    public function getProviderName(): string;

    /**
     * Verificar si el proveedor está configurado correctamente
     */
    public function isConfigured(): bool;
}