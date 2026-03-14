<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function render($component, $props = [])
    {
        return Inertia::render($component, array_merge([
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ], $props));
    }
}