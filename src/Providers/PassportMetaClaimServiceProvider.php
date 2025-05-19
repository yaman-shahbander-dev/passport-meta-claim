<?php

namespace PassportMetaClaim\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use PassportMetaClaim\Utilities\AccessToken;
use Laravel\Passport\Bridge\AccessTokenRepository as PassportAccessTokenRepository;
use PassportMetaClaim\Repositories\AccessTokenRepository;

class PassportMetaClaimServiceProvider extends ServiceProvider
{
     /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Load the configuration file
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/passport-meta-claim.php', 'passport-meta-claim'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the configuration file
         $this->publishes([
            __DIR__ . '/../../config/passport-meta-claim.php' => config_path('passport-meta-claim.php'),
        ], 'config');

        if (method_exists(Passport::class, 'useAccessTokenEntity')) {
             // Set custom AccessToken entity
            Passport::useAccessTokenEntity(AccessToken::class);
        } else {
            // Fallback for older Passport versions
            $this->app->bind(PassportAccessTokenRepository::class, AccessTokenRepository::class);
        }
    }
}