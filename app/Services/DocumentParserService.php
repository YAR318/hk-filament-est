<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class DocumentParserService
{
    /**
     * Extraer texto de un archivo según su extensión
     */
    public function extractText(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => $this->extractFromPdf($filePath),
            'docx', 'doc' => $this->extractFromWord($filePath),
            'txt' => $this->extractFromText($filePath),
            default => throw new \InvalidArgumentException("Formato no soportado: {$extension}"),
        };
    }

    /**
     * Extraer texto de un PDF
     */
    protected function extractFromPdf(string $filePath): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            return $this->cleanText($text);
        } catch (\Exception $e) {
            Log::error('Error extrayendo texto de PDF', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Error al procesar el PDF: {$e->getMessage()}");
        }
    }

    /**
     * Extraer texto de un archivo Word (.docx)
     */
    protected function extractFromWord(string $filePath): string
    {
        try {
            $phpWord = WordIOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractElementText($element) . "\n";
                }
            }

            return $this->cleanText($text);
        } catch (\Exception $e) {
            Log::error('Error extrayendo texto de Word', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Error al procesar el documento Word: {$e->getMessage()}");
        }
    }

    /**
     * Extraer texto de un elemento de PhpWord recursivamente
     */
    protected function extractElementText($element): string
    {
        if (method_exists($element, 'getText')) {
            $text = $element->getText();
            if (is_string($text)) {
                return $text;
            }
        }

        if (method_exists($element, 'getElements')) {
            $text = '';
            foreach ($element->getElements() as $child) {
                $text .= $this->extractElementText($child) . ' ';
            }
            return trim($text);
        }

        return '';
    }

    /**
     * Extraer texto de un archivo TXT
     */
    protected function extractFromText(string $filePath): string
    {
        $text = file_get_contents($filePath);
        if ($text === false) {
            throw new \RuntimeException("No se pudo leer el archivo de texto.");
        }

        return $this->cleanText($text);
    }

    /**
     * Limpiar texto extraído
     */
    protected function cleanText(string $text): string
    {
        // Eliminar caracteres nulos y de control
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);

        // Normalizar saltos de línea
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Reducir líneas vacías múltiples a máximo 2
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Reducir espacios múltiples
        $text = preg_replace('/[ \t]+/', ' ', $text);

        return trim($text);
    }
}
