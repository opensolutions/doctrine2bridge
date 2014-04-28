<?php

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */

namespace Doctrine2Bridge\Auth;

use Illuminate\Hashing\HasherInterface;

/**
 * Class to provide a Doctrine2 user object for Laravel authentication.
 */
class Doctrine2UserProvider implements \Illuminate\Auth\UserProviderInterface
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The repository (table) containing the users.
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $d2repository;


    /**
      * Create a new database user provider.
      *
      * @param  \Doctrine\ORM\EntityRepository       $d2repository The Doctrine2 repository (table) containing the users.
      * @param  \Illuminate\Hashing\HasherInterface  $hasher The hasher implementation
      * @return void
      */
    public function __construct( \Doctrine\ORM\EntityRepository $d2repository, HasherInterface $hasher )
    {
        $this->d2repository = $d2repository;
        $this->hasher       = $hasher;
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveById( $identifier )
    {
        return $this->d2repository->find( $identifier );
    }


    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Doctrine2 "user" entity object that will be utilised by the Guard instances.
        $qb = $this->d2repository->createQueryBuilder( 'u' )
                    ->select( 'u' )
                    ->setMaxResults( 1 );

        $i = 1;

        foreach( $credentials as $key => $value )
            if( !str_contains( $key, 'password' ) )
                $qb->andWhere( "u.{$key} = ?{$i}" )->setParameter( $i++, $value );

        // Now we are ready to execute the query to see if we have an user matching
        // the given credentials. If not, we will just return nulls and indicate
        // that there are no matching users for these given credential arrays.
        try {
            return $qb->getQuery()->getSingleResult();
        } catch( \Doctrine\ORM\NoResultException $e ) {
            return null;
        }
    }


    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials( \Illuminate\Auth\UserInterface $user, array $credentials )
    {
        return $this->hasher->check( $credentials['password'], $user->getAuthPassword() );
    }


    /**
     * Retrieve a user by their unique "remember me" token.
     *
     * @param mixed $identifier
     * @param string $token
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByToken( $identifier, $token )
    {
        try {
            return $this->d2repository
                ->createQueryBuilder( 'u' )
                ->setMaxResults( 1 )
                ->select( 'u' )
                ->andWhere( 'u.id = :id' )->setParameter( 'id', $identifier )
                ->andWhere( 'u.remember_token = :token' )->setParameter( 'token', $token )
                ->getQuery()
                ->getSingleResult();
        }
        catch( \Doctrine\ORM\NoResultException $e ) {
            return null;
        }
    }


    /**
     * Updates the "remember me" token for the given user in storage.
     *
     * @param \Illuminate\Auth\UserInterface $user
     * @param string $token
     * @return void
     */
    public function updateRememberToken( \Illuminate\Auth\UserInterface $user, $token )
    {
        $user->setRememberToken( $token );
        $this->d2repository->createQueryBuilder( 'dummy' )->getEntityManager()->flush();
    }

}
