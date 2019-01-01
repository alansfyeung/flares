<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $clientCredentialsHeaderName = 'X-Api-UsesClientCredentials';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();      // Web routes, public and authed

        $request = $this->app['request'];       // Inspect request to find out if API should expose client_credentials
        if ($request->hasHeader($this->clientCredentialsHeaderName)) {
            $this->mapApiClientCredentialsRoutes();     // API routes for oauth client_credentials
        } else {
            $this->mapApiRoutes();      // API routes with authentication
        }
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes()
    {
        Route::group(['middleware' => 'web', 'namespace' => $this->namespace], function ($router) {
            require base_path('routes/web.php');
        });

        Route::group(['middleware' => ['web', 'auth'], 'namespace' => $this->namespace], function ($router) {
            require base_path('routes/web-auth.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     */
    protected function mapApiRoutes()
    {
        Route::group(['middleware' => 'api', 'prefix' => 'api', 'namespace' => $this->namespace], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * For API routes that are accessed with a token from an oauth client_credentials flow
     */
    protected function mapApiClientCredentialsRoutes()
    {
        Route::group(['middleware' => 'apiClientCredentials', 'prefix' => 'api', 'namespace' => $this->namespace], function ($router) {
            require base_path('routes/api-clientcred.php');
        });
    }
}
