<?php 

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */


namespace Doctrine2Bridge\Support\Facades;

use Illuminate\Support\Facades\Facade;


class Doctrine2 extends Facade {

        /**
        * Get the registered name of the component.
        *
        * @return string
        */
        protected static function getFacadeAccessor() { return 'd2embridge'; }

}