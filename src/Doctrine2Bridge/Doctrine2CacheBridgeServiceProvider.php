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

class Doctrine2CacheBridgeServiceProvider extends ServiceProvider {

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

		$this->app->singleton( 'd2cachebridge', function ($app) {

			$cacheClass = "\Doctrine\Common\Cache\\" . \Config::get('doctrine2bridge::cache.type');

			if(!class_exists($cacheClass))
				throw new Exception\ImplementationNotFound( $cacheClass );

			$cache = new $cacheClass;

			if( \Config::has('doctrine2bridge::cache.namespace') )
                $cache->setNamespace( \Config::get('doctrine2bridge::cache.namespace') );

			switch( \Config::get('doctrine2bridge::cache.type' ) )
			{
				case 'MemcacheCache':
					$memcache = new \Memcache;

					if( !\Config::has('doctrine2bridge::cache.memcache.servers') || !count( \Config::get('doctrine2bridge::cache.memcache.servers') ) )
						throw new Exception\Configuration( 'No servers defined for Doctrine2Bridge\Doctrine2CacheBridgeServiceProvider - Memcache' );

					foreach( \Config::get('doctrine2bridge::cache.memcache.servers') as $server )
					{
						$memcache->addServer(
	                        $server['host'],
	                        isset( $server['port'] )         ? $server['port']         : 11211,
	                        isset( $server['persistent'] )   ? $server['persistent']   : false,
	                        isset( $server['weight'] )       ? $server['weight']       : 1,
	                        isset( $server['timeout'] )      ? $server['timeout']      : 1,
	                        isset( $server['retry_int'] )    ? $server['retry_int']    : 15
	                    );

	        	        $cache->setMemcache( $memcache );
					}
					break;
			}

			return $cache;
		});

		// Shortcut so developers don't need to add an Alias in app/config/app.php
		\App::booting( function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias( 'D2Cache', 'Doctrine2Bridge\Support\Facades\Doctrine2Cache' );
		});

	}

	/**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
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