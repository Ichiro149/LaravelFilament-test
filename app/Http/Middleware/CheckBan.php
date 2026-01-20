<?php

namespace App\Http\Middleware;

use App\Models\Ban;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Пропускаем страницу бана и logout
        if ($request->is('banned') || $request->is('logout') || $request->is('api/fingerprint')) {
            return $next($request);
        }

        // Пропускаем админ панель для администраторов
        if ($request->is('admin/*') || $request->is('seller/*')) {
            $user = $request->user();
            if ($user && in_array($user->role, ['super_admin', 'admin'])) {
                return $next($request);
            }
        }

        $userId = $request->user()?->id;
        $ip = $request->ip();
        $fingerprint = $request->cookie('device_fingerprint') ?? $request->header('X-Device-Fingerprint');

        // Проверяем все типы банов
        $ban = Ban::checkAllBans($userId, $ip, $fingerprint);

        if ($ban) {
            // Логируем попытку доступа
            $ban->logAccessAttempt(
                $userId,
                $ip,
                $fingerprint,
                $request->userAgent(),
                $request->fullUrl()
            );

            // Если это AJAX/API запрос
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'banned',
                    'message' => $ban->public_message ?? 'Your access has been restricted.',
                    'reason' => Ban::REASONS[$ban->reason] ?? $ban->reason,
                    'expires_at' => $ban->expires_at?->toIso8601String(),
                    'is_permanent' => $ban->isPermanent(),
                ], 403);
            }

            // Редирект на страницу бана
            return redirect()->route('banned')->with('ban', $ban);
        }

        return $next($request);
    }
}
