<?php

namespace Thotam\ThotamGmail;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thotam\ThotamGmail\Skeleton\SkeletonClass
 */
class ThotamGmailFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'thotam-gmail';
    }
}
