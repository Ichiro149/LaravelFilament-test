<?php

return [
    // Webhook resource
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'create_webhook' => 'Create Webhook',
    'edit_webhook' => 'Edit Webhook',

    // Fields
    'name' => 'Name',
    'url' => 'URL',
    'secret' => 'Secret Key',
    'secret_hint' => 'Used to sign webhook payloads with HMAC-SHA256',
    'events' => 'Events',
    'is_active' => 'Active',
    'max_retries' => 'Max Retries',
    'timeout_seconds' => 'Timeout (seconds)',
    'last_triggered_at' => 'Last Triggered',
    'deliveries' => 'Deliveries',

    // Events
    'event_order_created' => 'Order Created',
    'event_order_status_changed' => 'Order Status Changed',
    'event_refund_requested' => 'Refund Requested',
    'event_refund_status_changed' => 'Refund Status Changed',
    'event_ticket_created' => 'Ticket Created',
    'event_ticket_status_changed' => 'Ticket Status Changed',

    // Actions
    'test_webhook' => 'Test',
    'send_test' => 'Send Test Webhook',
    'test_description' => 'This will send a test payload to the webhook URL.',
    'view_logs' => 'View Logs',
    'generate_secret' => 'Generate',

    // Logs
    'logs' => 'Logs',
    'logs_for' => 'Logs for :name',
    'event' => 'Event',
    'status' => 'Status',
    'http_status' => 'HTTP Status',
    'attempt' => 'Attempt',
    'error_message' => 'Error Message',
    'sent_at' => 'Sent At',
    'retry' => 'Retry',
    'view_payload' => 'View Payload',
    'request_payload' => 'Request Payload',
    'response_body' => 'Response Body',

    // Status
    'status_pending' => 'Pending',
    'status_success' => 'Success',
    'status_failed' => 'Failed',

    // Messages
    'test_sent' => 'Test webhook sent successfully',
    'retry_scheduled' => 'Webhook retry scheduled',
    'never' => 'Never',
];
