<?php

namespace Umami;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Umami\UmamiFacade
 */
class UmamiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Umami::class;
    }
}
