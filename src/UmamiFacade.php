<?php

namespace Umami;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Umami\Skeleton\SkeletonClass
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
