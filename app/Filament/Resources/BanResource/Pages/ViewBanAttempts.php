<?php

namespace App\Filament\Resources\BanResource\Pages;

use App\Filament\Resources\BanResource;
use App\Models\Ban;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ViewBanAttempts extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = BanResource::class;

    protected static string $view = 'filament.resources.ban-resource.pages.view-ban-attempts';

    public Ban $record;

    public function mount(Ban $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Access Attempts - Ban #{$this->record->id}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->accessAttempts()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('attempted_at')
                    ->label('Date & Time')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('Guest'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->copyable(),

                Tables\Columns\TextColumn::make('fingerprint')
                    ->label('Fingerprint')
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->fingerprint)
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('attempted_at', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }
}
