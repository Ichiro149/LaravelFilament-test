<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Ban Info Card -->
        <x-filament::section>
            <x-slot name="heading">
                Ban Information
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <p class="font-medium">{{ \App\Models\Ban::TYPES[$record->type] ?? $record->type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Value</p>
                    <p class="font-medium font-mono">{{ $record->value }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Reason</p>
                    <p class="font-medium">{{ \App\Models\Ban::REASONS[$record->reason] ?? $record->reason }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium">
                        @if($record->is_active && !$record->isExpired())
                            <span class="text-red-500">Active</span>
                        @elseif($record->isExpired())
                            <span class="text-yellow-500">Expired</span>
                        @else
                            <span class="text-gray-500">Inactive</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Expires</p>
                    <p class="font-medium">{{ $record->expires_at ? $record->expires_at->format('d.m.Y H:i') : 'Permanent' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                    <p class="font-medium">{{ $record->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Access Attempts Table -->
        <x-filament::section>
            <x-slot name="heading">
                Access Attempts ({{ $record->accessAttempts()->count() }})
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
