<?php

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */


namespace Doctrine2Bridge;

class Doctrine2BridgeServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Configuration from file
     */
    protected $d2config = null;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package( 'opensolutions/doctrine2bridge', 'doctrine2bridge' );
		$this->app['config']->package('opensolutions/doctrine2bridge', __DIR__.'/../config');

        $d2config = $this->d2config = \Config::get('doctrine2bridge::doctrine');

        $this->app->singleton( 'd2embridge', function ($app) use($d2config) {

            $dconfig = new \Doctrine\ORM\Configuration;


            $driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver(
                array( $d2config['paths']['xml_schema'] )
            );

            $dconfig->setMetadataDriverImpl( $driver );

            $dconfig->setProxyDir(                 $d2config['paths']['proxies']      );
            $dconfig->setProxyNamespace(           $d2config['namespaces']['proxies'] );
            $dconfig->setAutoGenerateProxyClasses( $d2config['autogen_proxies']       );

            return \Doctrine\ORM\EntityManager::create( $d2config['connection'], $dconfig );
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        \App::booting( function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias( 'D2EM', 'Doctrine2Bridge\Support\Facades\Doctrine2' );
        });
	}

	/**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $d2em = $this->app['d2embridge'];

        $d2cache = $this->app['d2cachebridge'];
        $d2em->getConfiguration()->setMetadataCacheImpl( $d2cache );
        $d2em->getConfiguration()->setQueryCacheImpl( $d2cache );
        $d2em->getConnection()->getConfiguration()->setResultCacheImpl( $d2cache );

        if( isset( $this->d2config['sqllogger']['enabled'] ) && $this->d2config['sqllogger']['enabled'] )
        {
            $logger = new Logger\Laravel;
            if( isset( $this->d2config['sqllogger']['level'] ) )
                $logger->setLevel( $this->d2config['sqllogger']['level'] );

            $d2em->getConnection()->getConfiguration()->setSQLLogger( $logger );
        }

        if( isset( $this->d2config['auth']['enabled'] ) && $this->d2config['auth']['enabled'] )
        {
            \Auth::extend( 'doctrine2bridge', function()
            {
                return new \Illuminate\Auth\Guard(
                    new Auth\Doctrine2UserProvider(
                        \D2EM::getRepository( $this->d2config['auth']['entity'] ),
                        new \Illuminate\Hashing\BcryptHasher
                    ),
                    \App::make('session.store')
                );
            });
        }
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
