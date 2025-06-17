<?php

namespace App\Providers;

use App\Models\Agreement;
use App\Models\Client;
use App\Models\SalesRep;
use App\Policies\AgreementPolicy;
use App\Policies\ClientPolicy;
use App\Policies\SalesRepPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SalesRep::class => SalesRepPolicy::class,
        Client::class => ClientPolicy::class,
        Agreement::class => AgreementPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

    }
}
