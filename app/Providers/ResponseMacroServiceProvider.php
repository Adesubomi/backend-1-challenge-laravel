<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function (string $message, mixed $data=null) {
            return Response::json([
                'message' => $message,
                'data' => $data ?? []
            ]);
        });

        Response::macro('created', function (string $message, mixed $data) {
            return Response::json([
                'message' => $message,
                'data' => $data
            ], 201);
        });

        Response::macro('failed', function (string $message, mixed $data, int $statusCode=400) {
            return Response::json([
                'message' => $message,
                'data' => $data
            ], $statusCode);
        });
    }
}
