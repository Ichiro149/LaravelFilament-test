<?php

namespace App\Observers;

use App\Jobs\DispatchWebhookJob;
use App\Models\Order;
use App\Models\Webhook;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->dispatchWebhook(Webhook::EVENT_ORDER_CREATED, $order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status changed
        if ($order->wasChanged('order_status')) {
            $webhooks = Webhook::forEvent(Webhook::EVENT_ORDER_STATUS_CHANGED);

            $payload = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $order->getOriginal('order_status'),
                'new_status' => $order->order_status,
                'customer_email' => $order->customer_email,
                'total' => $order->total,
            ];

            foreach ($webhooks as $webhook) {
                DispatchWebhookJob::dispatch($webhook, Webhook::EVENT_ORDER_STATUS_CHANGED, $payload);
            }
        }
    }

    /**
     * Dispatch webhook for order.
     */
    protected function dispatchWebhook(string $event, Order $order): void
    {
        $webhooks = Webhook::forEvent($event);

        $payload = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->order_status,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'shipping_address' => $order->shipping_address,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount_amount,
            'total' => $order->total,
            'items_count' => $order->items()->count(),
            'created_at' => $order->created_at?->toIso8601String(),
        ];

        foreach ($webhooks as $webhook) {
            DispatchWebhookJob::dispatch($webhook, $event, $payload);
        }
    }
}
