<?php

namespace App\Filament\Resources\WebhookResource\Pages;

use App\Filament\Resources\WebhookResource;
use App\Models\Webhook;
use App\Models\WebhookLog;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebhookLogs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = WebhookResource::class;

    protected static string $view = 'filament.resources.webhook-resource.pages.webhook-logs';

    public Webhook $record;

    public function mount(Webhook $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Logs for {$this->record->name}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => WebhookLog::query()->where('webhook_id', $this->record->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'order.created' => 'success',
                        'order.status_changed' => 'info',
                        'webhook.test' => 'gray',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('response_status')
                    ->label('HTTP Status')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('attempt')
                    ->label('Attempt'),

                Tables\Columns\TextColumn::make('error_message')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('event')
                    ->options(Webhook::AVAILABLE_EVENTS),
            ])
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (WebhookLog $record) => $record->canRetry())
                    ->action(function (WebhookLog $record) {
                        $service = app(\App\Services\WebhookService::class);
                        $service->retry($record);
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('view_payload')
                    ->icon('heroicon-o-code-bracket')
                    ->modalContent(fn (WebhookLog $record) => view('filament.resources.webhook-resource.pages.payload-modal', [
                        'payload' => json_encode($record->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        'response' => $record->response_body,
                    ]))
                    ->modalHeading('Payload Details')
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([])
            ->poll('10s');
    }
}
