<?php namespace Williamson\Laragram\Laravel;

use Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Log;
use Williamson\Laragram\TgCommands;
use Williamson\Laragram\ClientException;

class LaragramServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $loader  = AliasLoader::getInstance();
        $aliases = Config::get('app.aliases');
        if (empty($aliases['TG'])) {
            $loader->alias('TG', 'Williamson\Laragram\Laravel\LaragramFacade');
        }
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
}
