<?php namespace Williamson\Laragram\Facades;

use Illuminate\Support\Facades\Facade;

class Laragram extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laragram';
    }

} 