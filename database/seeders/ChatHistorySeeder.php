<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\ChatHistoryService;

class ChatHistorySeeder extends Seeder
{
    public function run(): void
    {
        $service = new ChatHistoryService();

        // Conversación 1: Cliente preguntando por productos
        $phone1 = '+5213331234567';
        $service->addUserMessage($phone1, 'Hola, buenos días');
        $service->addAssistantMessage($phone1, '¡Hola! Buenos días. ¿En qué puedo ayudarte hoy?');
        $service->addUserMessage($phone1, 'Quiero información sobre sus productos');
        $service->addAssistantMessage($phone1, 'Claro, tenemos varios productos disponibles. ¿Qué tipo de producto te interesa?');
        $service->addUserMessage($phone1, 'Me interesan las laptops');
        $service->addAssistantMessage($phone1, 'Excelente elección. Tenemos laptops desde $8,000 hasta $25,000 pesos. ¿Qué uso le darás?');

        // Actualizar nombre del contacto
        $conv1 = $service->getOrCreateConversation($phone1);
        $conv1->update(['contact_name' => 'Juan Pérez']);

        // Conversación 2: Cliente con problema técnico
        $phone2 = '+5213339876543';
        $service->addUserMessage($phone2, 'Tengo un problema con mi pedido');
        $service->addAssistantMessage($phone2, 'Lamento escuchar eso. ¿Podrías darme más detalles sobre el problema?');
        $service->addUserMessage($phone2, 'No me ha llegado mi paquete');
        $service->addAssistantMessage($phone2, 'Entiendo tu preocupación. ¿Cuál es tu número de pedido?');
        $service->addUserMessage($phone2, 'Es el #12345');
        $service->addAssistantMessage($phone2, 'Déjame revisar... Tu pedido está en camino y llegará mañana antes de las 6pm.');

        $conv2 = $service->getOrCreateConversation($phone2);
        $conv2->update(['contact_name' => 'María González']);

        // Conversación 3: Cliente nuevo
        $phone3 = '+5213335551234';
        $service->addUserMessage($phone3, 'Hola');
        $service->addAssistantMessage($phone3, '¡Hola! Bienvenido. ¿En qué puedo ayudarte?');

        $conv3 = $service->getOrCreateConversation($phone3);
        $conv3->update(['contact_name' => 'Carlos Ramírez']);

        // Conversación 4: Conversación archivada
        $phone4 = '+5213337778888';
        $service->addUserMessage($phone4, 'Gracias por todo');
        $service->addAssistantMessage($phone4, '¡De nada! Fue un placer ayudarte. ¡Hasta pronto!');
        
        $conv4 = $service->getOrCreateConversation($phone4);
        $conv4->update([
            'contact_name' => 'Ana López',
            'status' => 'archived'
        ]);

        // Conversación 5: Conversación larga
        $phone5 = '+5213334445555';
        $messages = [
            ['user', 'Hola, necesito ayuda'],
            ['assistant', '¡Hola! Claro, estoy aquí para ayudarte. ¿Qué necesitas?'],
            ['user', 'Quiero hacer una compra'],
            ['assistant', 'Perfecto. ¿Qué producto te interesa?'],
            ['user', 'Un celular'],
            ['assistant', 'Tenemos varios modelos. ¿Qué marca prefieres?'],
            ['user', 'Samsung'],
            ['assistant', 'Excelente elección. Tenemos el Galaxy S23, S24 y A54. ¿Cuál te interesa?'],
            ['user', 'El S24'],
            ['assistant', 'El Samsung Galaxy S24 cuesta $18,999. ¿Te gustaría comprarlo?'],
            ['user', 'Sí, ¿tienen en color negro?'],
            ['assistant', 'Sí, tenemos disponible en negro. ¿Procedo con la compra?'],
            ['user', 'Sí por favor'],
            ['assistant', 'Perfecto. Te enviaré el link de pago por WhatsApp.'],
        ];

        foreach ($messages as $msg) {
            if ($msg[0] === 'user') {
                $service->addUserMessage($phone5, $msg[1]);
            } else {
                $service->addAssistantMessage($phone5, $msg[1]);
            }
        }

        $conv5 = $service->getOrCreateConversation($phone5);
        $conv5->update(['contact_name' => 'Roberto Sánchez']);

        $this->command->info('✅ Historial de chat creado correctamente');
        $this->command->info('📊 5 conversaciones con múltiples mensajes');
    }
}
