<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Download invoice for an order (for authenticated users or guest orders).
     */
    public function download(Order $order)
    {
        $user = Auth::user();

        // Guest order (user_id = null) - allow if session matches
        if ($order->user_id === null) {
            $sessionOrderId = session('last_order_id');
            if ($sessionOrderId != $order->id) {
                abort(403, __('invoice.errors.access_denied'));
            }

            return $this->generatePdf($order);
        }

        // For orders with user_id - require authentication
        if (! $user) {
            return redirect()->route('login');
        }

        // Check: only owner or admin
        if ($user->id !== $order->user_id && ! $user->isAdmin()) {
            abort(403, __('invoice.errors.access_denied'));
        }

        return $this->generatePdf($order);
    }

    /**
     * Download invoice by order number (requires email verification).
     *
     * This provides a secure way to access invoices without full authentication
     * by requiring the customer email to match the order.
     */
    public function downloadByNumber(Request $request, string $orderNumber)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Security check: email must match the order's customer email
        if (strtolower($order->customer_email) !== strtolower($request->email)) {
            abort(403, __('invoice.errors.email_mismatch'));
        }

        return $this->generatePdf($order);
    }

    /**
     * View invoice in browser.
     */
    public function view(Order $order)
    {
        $user = Auth::user();

        // Guest order - allow if session matches
        if ($order->user_id === null) {
            $sessionOrderId = session('last_order_id');
            if ($sessionOrderId != $order->id) {
                abort(403, __('invoice.errors.access_denied'));
            }

            return $this->generatePdf($order, false);
        }

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->id !== $order->user_id && ! $user->isAdmin()) {
            abort(403, __('invoice.errors.access_denied'));
        }

        return $this->generatePdf($order, false);
    }

    /**
     * Generate PDF invoice.
     */
    private function generatePdf(Order $order, bool $download = true)
    {
        $order->load(['items.product', 'user', 'status']);

        $data = [
            'order' => $order,
            'company' => config('invoice.company'),
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('invoices.template', $data);

        $pdfConfig = config('invoice.pdf', []);
        $pdf->setPaper(
            $pdfConfig['paper'] ?? 'a4',
            $pdfConfig['orientation'] ?? 'portrait'
        );

        $filename = 'invoice-'.$order->order_number.'.pdf';

        if ($download) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
