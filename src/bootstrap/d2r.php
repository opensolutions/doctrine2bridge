<?php

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */

/**
 * Convenience function for getting Doctrine2 repository instances
 *
 * @param string $entity The name of the entity to load the repository for
 * @param string $namespace The entities namespace
 * @return Doctrine\ORM\EntityRepository An instance of the repository
 */ 
function D2R( $entity, $namespace = 'Entities' )
{
    return D2EM::getRepository( $namespace . '\\' . $entity );
}
