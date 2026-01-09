<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\CustomerReview;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\RefundRequest;
use App\Models\Ticket;
use App\Models\UserAddress;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerReviewPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentMethodPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RefundRequestPolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserAddressPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        CustomerReview::class => CustomerReviewPolicy::class,
        Order::class => OrderPolicy::class,
        PaymentMethod::class => PaymentMethodPolicy::class,
        Product::class => ProductPolicy::class,
        RefundRequest::class => RefundRequestPolicy::class,
        Ticket::class => TicketPolicy::class,
        UserAddress::class => UserAddressPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
