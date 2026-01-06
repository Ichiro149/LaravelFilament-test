<?php

return [
    // Webhook resource
    'webhook' => 'Вебхук',
    'webhooks' => 'Вебхуки',
    'create_webhook' => 'Создать вебхук',
    'edit_webhook' => 'Редактировать вебхук',

    // Fields
    'name' => 'Название',
    'url' => 'URL',
    'secret' => 'Секретный ключ',
    'secret_hint' => 'Используется для подписи данных с помощью HMAC-SHA256',
    'events' => 'События',
    'is_active' => 'Активен',
    'max_retries' => 'Макс. попыток',
    'timeout_seconds' => 'Таймаут (сек)',
    'last_triggered_at' => 'Последний запуск',
    'deliveries' => 'Доставки',

    // Events
    'event_order_created' => 'Заказ создан',
    'event_order_status_changed' => 'Статус заказа изменён',
    'event_refund_requested' => 'Запрос на возврат',
    'event_refund_status_changed' => 'Статус возврата изменён',
    'event_ticket_created' => 'Тикет создан',
    'event_ticket_status_changed' => 'Статус тикета изменён',

    // Actions
    'test_webhook' => 'Тест',
    'send_test' => 'Отправить тестовый запрос',
    'test_description' => 'Будет отправлен тестовый запрос на URL вебхука.',
    'view_logs' => 'Просмотр логов',
    'generate_secret' => 'Сгенерировать',

    // Logs
    'logs' => 'Логи',
    'logs_for' => 'Логи для :name',
    'event' => 'Событие',
    'status' => 'Статус',
    'http_status' => 'HTTP статус',
    'attempt' => 'Попытка',
    'error_message' => 'Сообщение об ошибке',
    'sent_at' => 'Отправлено',
    'retry' => 'Повторить',
    'view_payload' => 'Просмотр данных',
    'request_payload' => 'Данные запроса',
    'response_body' => 'Ответ сервера',

    // Status
    'status_pending' => 'Ожидание',
    'status_success' => 'Успешно',
    'status_failed' => 'Ошибка',

    // Messages
    'test_sent' => 'Тестовый запрос отправлен',
    'retry_scheduled' => 'Повторная отправка запланирована',
    'never' => 'Никогда',
];
