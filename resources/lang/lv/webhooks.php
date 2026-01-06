<?php

return [
    // Webhook resource
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'create_webhook' => 'Izveidot webhook',
    'edit_webhook' => 'Rediģēt webhook',

    // Fields
    'name' => 'Nosaukums',
    'url' => 'URL',
    'secret' => 'Slepenā atslēga',
    'secret_hint' => 'Izmanto datu parakstīšanai ar HMAC-SHA256',
    'events' => 'Notikumi',
    'is_active' => 'Aktīvs',
    'max_retries' => 'Maks. mēģinājumi',
    'timeout_seconds' => 'Taimauts (sek)',
    'last_triggered_at' => 'Pēdējoreiz aktivizēts',
    'deliveries' => 'Piegādes',

    // Events
    'event_order_created' => 'Pasūtījums izveidots',
    'event_order_status_changed' => 'Pasūtījuma statuss mainīts',
    'event_refund_requested' => 'Atmaksa pieprasīta',
    'event_refund_status_changed' => 'Atmaksas statuss mainīts',
    'event_ticket_created' => 'Biļete izveidota',
    'event_ticket_status_changed' => 'Biļetes statuss mainīts',

    // Actions
    'test_webhook' => 'Tests',
    'send_test' => 'Nosūtīt testa pieprasījumu',
    'test_description' => 'Tiks nosūtīts testa pieprasījums uz webhook URL.',
    'view_logs' => 'Skatīt žurnālus',
    'generate_secret' => 'Ģenerēt',

    // Logs
    'logs' => 'Žurnāli',
    'logs_for' => 'Žurnāli priekš :name',
    'event' => 'Notikums',
    'status' => 'Statuss',
    'http_status' => 'HTTP statuss',
    'attempt' => 'Mēģinājums',
    'error_message' => 'Kļūdas ziņojums',
    'sent_at' => 'Nosūtīts',
    'retry' => 'Mēģināt vēlreiz',
    'view_payload' => 'Skatīt datus',
    'request_payload' => 'Pieprasījuma dati',
    'response_body' => 'Atbildes saturs',

    // Status
    'status_pending' => 'Gaida',
    'status_success' => 'Veiksmīgi',
    'status_failed' => 'Neizdevās',

    // Messages
    'test_sent' => 'Testa pieprasījums nosūtīts',
    'retry_scheduled' => 'Atkārtots mēģinājums ieplānots',
    'never' => 'Nekad',
];
