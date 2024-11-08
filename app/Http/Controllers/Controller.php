<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Integration Swagger in Project",
 *      description="Implementation of Swagger with in Project",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Nginx",
 *          url="https://www.nginx.com/"
 *      ),
 * )
 *
 *
 */
class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
