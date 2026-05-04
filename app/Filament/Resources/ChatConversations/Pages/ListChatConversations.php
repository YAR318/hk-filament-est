<?php

namespace App\Filament\Resources\ChatConversations\Pages;

use App\Filament\Resources\ChatConversations\ChatConversationResource;
use App\Models\Operator;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListChatConversations extends ListRecords
{
    protected static string $resource = ChatConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        // Admin y Supervisor ven todas las conversaciones
        if ($user->can('ver_todas_conversaciones')) {
            return $query;
        }

        // Operador ve solo sus chats asignados + los sin asignar
        $operator = Operator::where('email', $user->email)->first();
        $operatorId = $operator?->id;

        return $query->where(function (Builder $q) use ($operatorId) {
            $q->whereNull('assigned_to') // Sin asignar
              ->orWhere('assigned_to', $operatorId); // Asignados a mí
        });
    }
}