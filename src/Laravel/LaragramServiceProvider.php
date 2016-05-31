<?php namespace Williamson\Laragram\Laravel;

use Log;
use Config;
use Williamson\Laragram\TgCommands;
use Illuminate\Support\ServiceProvider;
use Williamson\Laragram\ClientException;

class LaragramServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Williamson\Laragram\TgCommands', function () {
            try {
                $tg = new TgCommands(Config::get('services.telegram.socket'));
            } catch (ClientException $e) {
                Log::error($e->getMessage());

                return;
            }

            return $tg;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TgCommands::class];
    }
}
