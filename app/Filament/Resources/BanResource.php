<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BanResource\Pages;
use App\Models\Ban;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BanResource extends Resource
{
    protected static ?string $model = Ban::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';

    protected static ?string $navigationLabel = 'User Bans';

    protected static ?string $navigationGroup = 'Users & Sellers';

    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ban Configuration')
                    ->description('Configure the ban parameters')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Ban Type')
                            ->options(Ban::TYPES)
                            ->required()
                            ->live()
                            ->default('account')
                            ->helperText(fn (Get $get) => match ($get('type')) {
                                'account' => 'Ban specific user account',
                                'ip' => 'Ban by IP address - affects all users from this IP',
                                'fingerprint' => 'Ban by device fingerprint - most effective against ban evasion',
                                default => '',
                            }),

                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->username
                                ? "{$record->name} (@{$record->username}) ({$record->email})"
                                : "{$record->name} ({$record->email})")
                            ->searchable(['name', 'email', 'username'])
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'account')
                            ->visible(fn (Get $get) => $get('type') === 'account')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('value', (string) $state);
                                }
                            }),

                        Forms\Components\TextInput::make('value')
                            ->label(fn (Get $get) => match ($get('type')) {
                                'ip' => 'IP Address',
                                'fingerprint' => 'Device Fingerprint',
                                default => 'Value',
                            })
                            ->required()
                            ->visible(fn (Get $get) => in_array($get('type'), ['ip', 'fingerprint']))
                            ->helperText(fn (Get $get) => match ($get('type')) {
                                'ip' => 'Enter IP address (e.g., 192.168.1.1) or CIDR range (e.g., 192.168.1.0/24)',
                                'fingerprint' => 'Enter device fingerprint hash',
                                default => '',
                            }),

                        Forms\Components\Select::make('reason')
                            ->label('Ban Reason')
                            ->options(Ban::REASONS)
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('duration')
                            ->label('Duration')
                            ->options(Ban::DURATIONS)
                            ->default('permanent')
                            ->live()
                            ->required()
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->visible(fn (Get $get) => $get('duration') === 'custom')
                            ->required(fn (Get $get) => $get('duration') === 'custom')
                            ->minDate(now())
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Messages')
                    ->schema([
                        Forms\Components\Textarea::make('public_message')
                            ->label('Public Message')
                            ->helperText('This message will be shown to the banned user')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\Textarea::make('admin_comment')
                            ->label('Admin Comment (Private)')
                            ->helperText('Internal notes - not visible to the user')
                            ->rows(3)
                            ->maxLength(2000),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Deactivate to remove the ban without deleting the record'),
                    ])
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state) => Ban::TYPES[$state] ?? $state)
                    ->colors([
                        'warning' => 'account',
                        'danger' => 'ip',
                        'purple' => 'fingerprint',
                    ]),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('N/A')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->value)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('reason')
                    ->label('Reason')
                    ->formatStateUsing(fn (string $state) => Ban::REASONS[$state] ?? $state)
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Permanent')
                    ->sortable()
                    ->color(fn ($record) => $record->isPermanent() ? 'danger' : ($record->isExpired() ? 'gray' : 'warning')),

                Tables\Columns\TextColumn::make('bannedByUser.name')
                    ->label('Banned By')
                    ->placeholder('System')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('access_attempts_count')
                    ->counts('accessAttempts')
                    ->label('Attempts')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Ban::TYPES),

                Tables\Filters\SelectFilter::make('reason')
                    ->options(Ban::REASONS),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->placeholder('All'),

                Tables\Filters\Filter::make('permanent')
                    ->label('Permanent bans')
                    ->query(fn (Builder $query) => $query->whereNull('expires_at'))
                    ->toggle(),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired bans')
                    ->query(fn (Builder $query) => $query->where('expires_at', '<=', now()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('unban')
                    ->label('Unban')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unban User')
                    ->modalDescription('Are you sure you want to remove this ban?')
                    ->form([
                        Forms\Components\Textarea::make('unban_reason')
                            ->label('Reason for unban')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Ban $record, array $data) {
                        $record->unban(Auth::id(), $data['unban_reason']);
                    })
                    ->visible(fn (Ban $record) => $record->is_active && ! $record->isExpired()),

                Tables\Actions\Action::make('viewAttempts')
                    ->label('View Attempts')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Ban $record) => static::getUrl('attempts', ['record' => $record]))
                    ->visible(fn (Ban $record) => $record->accessAttempts()->exists()),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()?->role === 'super_admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->role === 'super_admin'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBans::route('/'),
            'create' => Pages\CreateBan::route('/create'),
            'edit' => Pages\EditBan::route('/{record}/edit'),
            'attempts' => Pages\ViewBanAttempts::route('/{record}/attempts'),
        ];
    }
}
