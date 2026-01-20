<?php

namespace App\Filament\Resources\BanResource\Pages;

use App\Filament\Resources\BanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditBan extends EditRecord
{
    protected static string $resource = BanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('unban')
                ->label('Unban')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Unban User')
                ->form([
                    \Filament\Forms\Components\Textarea::make('unban_reason')
                        ->label('Reason for unban')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->unban(Auth::id(), $data['unban_reason']);

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('User unbanned')
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => $this->record->is_active && ! $this->record->isExpired()),

            Actions\DeleteAction::make()
                ->visible(fn () => Auth::user()?->role === 'super_admin'),
        ];
    }
}
