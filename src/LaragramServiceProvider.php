<?php namespace Williamson\Laragram;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class LaragramServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
//        $this->package('williamson/laragram', null, __DIR__);

//        $loader  = AliasLoader::getInstance();
//        $aliases = Config::get('app.aliases');
//        if (empty($aliases['TG']))
//        {
//            $loader->alias('TG', 'Williamson\Laragram\Facades\LaragramFacade');
//        }
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
                $tg = new TgCommands('unix:///tmp/tg.sck');
            } catch (ClientException $e) {
                Log::error($e->getMessage());

                return;
            }

            return $tg;
        });
    }
}