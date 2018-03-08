<?php namespace Hht\Bitcoin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hht\Bitcoin\Bitcoiner\BitcoinerManager
 */
class Bitcoin extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {

        return 'bitcoiner';

    }

}
