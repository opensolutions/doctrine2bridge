<?php

/**
 * Doctrine2 Bridge - Brings Doctrine2 to Laravel 4.
 *
 * @author Barry O'Donovan <barry@opensolutions.ie>
 * @copyright Copyright (c) 2014 Open Source Solutions Limited
 * @license MIT
 */


namespace Doctrine2Bridge;

use Illuminate\Support\ServiceProvider;

//use Doctrine\Common\EventManager;
//use Doctrine\ORM\Tools\Setup;
//use Doctrine\ORM\Events;
//use Doctrine\ORM\EntityManager;
//use Doctrine\ORM\Configuration;

class Doctrine2BridgeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package( 'opensolutions/doctrine2bridge', 'doctrine2bridge' );
		$this->app['config']->package('opensolutions/doctrine2bridge', __DIR__.'/../config');
	}

	/**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() 
    {
        $this->app->singleton( 'd2embridge', function ($app) {

            $config = \Config::get('doctrine2bridge::doctrine');

            $d2cache = $this->app['d2cachebridge'];

            $dconfig = new \Doctrine\ORM\Configuration;

            $dconfig->setMetadataCacheImpl( $d2cache );

            $driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver(
                [ $config['paths']['xml_schema'] ]
            );

            $dconfig->setMetadataDriverImpl( $driver );

            $dconfig->setQueryCacheImpl(           $d2cache                         );
            $dconfig->setResultCacheImpl(          $d2cache                         );
            $dconfig->setProxyDir(                 $config['paths']['proxies']      );
            $dconfig->setProxyNamespace(           $config['namespaces']['proxies'] );
            $dconfig->setAutoGenerateProxyClasses( $config['autogen_proxies']       );
            
            if( isset( $config['sqllogger']['enabled'] ) && $config['sqllogger']['enabled'] )
            {
                $logger = new Logger\Laravel;
                if( isset( $config['sqllogger']['level'] ) )
                    $logger->setLevel( $config['sqllogger']['level'] );
                    
                $dconfig->setSQLLogger( $logger );
            }

            return \Doctrine\ORM\EntityManager::create( $config['connection'], $dconfig );
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        \App::booting( function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias( 'D2EM', 'Doctrine2Bridge\Support\Facades\Doctrine2' );
        });
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