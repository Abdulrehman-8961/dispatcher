<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'SaveOwner',
        'SaveTruck',
        'SaveDriver',
        'getOwnerDetails',
        'getDriverDetails',
        'getTruckDetails',
        'getDispatcherDetails',
        'loadTrucks',
        'Change-Status',
        'getNonDispatchTrucks',
        'Upload',
        'saveTruckAccountInfo',
        'getWeeksByTruck',
        'getCompanyTrucks',
        'getYear'
    ];
}
