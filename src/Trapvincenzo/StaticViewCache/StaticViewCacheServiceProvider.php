<?php namespace Trapvincenzo\StaticViewCache;

use Illuminate\Support\ServiceProvider;

class StaticViewCacheServiceProvider extends ServiceProvider {

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
		$this->app['static-view-cache'] = $this->app->share(function($app)
		{
			$paths = $app['config']['view.paths'];
			return new \Trapvincenzo\StaticViewCache\StaticViewCache($app['files'],  $paths);
		});
	}

	public function boot()
	{
		$this->package('trapvincenzo/static-view-cache');

		$this->app->booting(function() {
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('StaticViewCache', 'Trapvincenzo\StaticViewCache\StaticViewCacheFacade');
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
