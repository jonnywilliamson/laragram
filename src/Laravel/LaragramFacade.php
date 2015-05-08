<?php namespace Williamson\Laragram\Laravel;

use Illuminate\Support\Facades\Facade;

class LaragramFacade extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Williamson\Laragram\TgCommands';
    }

} 