<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bans', function (Blueprint $table) {
            $table->id();

            // Тип бана: account, ip, fingerprint
            $table->enum('type', ['account', 'ip', 'fingerprint']);

            // Значение для бана (user_id для account, IP для ip, fingerprint для fingerprint)
            $table->string('value');

            // Связь с пользователем (если бан по аккаунту)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Причина бана (выбор из списка)
            $table->string('reason');

            // Комментарий администратора (приватный)
            $table->text('admin_comment')->nullable();

            // Публичное сообщение для пользователя
            $table->text('public_message')->nullable();

            // Срок действия (null = бессрочный)
            $table->timestamp('expires_at')->nullable();

            // Кто забанил
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();

            // Активен ли бан
            $table->boolean('is_active')->default(true);

            // Кто разбанил (если был разбанен досрочно)
            $table->foreignId('unbanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unbanned_at')->nullable();
            $table->text('unban_reason')->nullable();

            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index(['type', 'value', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
        });

        // Таблица для отслеживания fingerprints пользователей
        Schema::create('user_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('components')->nullable(); // Компоненты fingerprint
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->unique(['user_id', 'fingerprint']);
            $table->index('fingerprint');
            $table->index('ip_address');
        });

        // Лог попыток доступа заблокированных пользователей
        Schema::create('ban_access_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ban_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address');
            $table->string('fingerprint')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('attempted_at');

            $table->index(['ban_id', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ban_access_attempts');
        Schema::dropIfExists('user_fingerprints');
        Schema::dropIfExists('bans');
    }
};
