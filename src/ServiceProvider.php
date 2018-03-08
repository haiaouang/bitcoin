<?php namespace Hht\Bitcoin;

use Illuminate\Support\ServiceProvider as ServiceProviderSupport;

class ServiceProvider extends ServiceProviderSupport
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->registerBitcoiner();

	}

    /**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {

		$this->publishes([

            __DIR__ . '/config/bitcoiners.php' => config_path('bitcoiners.php')

        ]);

	}


	/**
	 * Register the driver based bitcoiner.
	 *
	 * @return void
	 */
	protected function registerBitcoiner() {

		$this->registerManager();

		$this->app->singleton('bitcoiner.bitcoiner', function () {
			return $this->app['bitcoiner']->launcher($this->getDefaultDriver());
		});

	}

	/**
	 * Register the bitcoiner manager.
	 *
	 * @return void
	 */
	protected function registerManager() {

		$this->app->singleton('bitcoiner', function () {
			return new BitcoinerManager($this->app);
		});

	}

	/**
	 * Get the default push driver.
	 *
	 * @return string
	 */
	protected function getDefaultDriver() {

		return $this->app['config']['bitcoiners.default'];

	}

}
