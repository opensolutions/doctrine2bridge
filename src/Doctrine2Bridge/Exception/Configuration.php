<?php

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */

namespace Doctrine2Bridge\Exception;

class Configuration extends \Exception {

    public function __construct( $message = null, $code = 0, Exception $previous = null )
    {
        return parent::__construct(
            $message, $code, $previous
        );
    }

}
