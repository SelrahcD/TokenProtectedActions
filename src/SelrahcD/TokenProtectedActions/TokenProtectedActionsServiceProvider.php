<?php namespace SelrahcD\TokenProtectedActions;

use Illuminate\Support\ServiceProvider;

class TokenProtectedActionsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('selrahcd/tokenprotectedactions');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRepository();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('protectedAction.repository');
	}


	protected function registerRepository()
	{
		$this->app['protectedAction.token.repository'] = $this->app->share(function($app)
		{
			$connection = $app['db']->connection();

			$key = $app['config']['app.key'];

			return new DatabaseTokenRepository($connection, 'action_tokens', $key, 259200);
		});
	}

}