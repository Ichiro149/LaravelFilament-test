<?php

namespace App\Filament\Resources\BanResource\Pages;

use App\Filament\Resources\BanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBans extends ListRecords
{
    protected static string $resource = BanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deactivateExpired')
                ->label('Deactivate Expired')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Deactivate Expired Bans')
                ->modalDescription('This will deactivate all bans that have passed their expiration date.')
                ->action(function () {
                    $count = \App\Models\Ban::deactivateExpiredBans();

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Expired bans deactivated')
                        ->body("{$count} ban(s) have been deactivated.")
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
