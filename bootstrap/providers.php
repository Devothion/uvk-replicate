<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    App\Providers\EnvKitTrustProxies::class,
    AppServiceProvider::class,
    FortifyServiceProvider::class,
];
