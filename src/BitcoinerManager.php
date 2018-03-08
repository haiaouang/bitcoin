<?php namespace Hht\Bitcoin;

use Closure;
use InvalidArgumentException;
use Hht\Bitcoin\Contracts\Bitcoiner as BitcoinerContract;
use Hht\Bitcoin\Contracts\Factory as FactoryContract;
use Hht\Bitcoin\Core\Bitcoiner as BitcoinerCore;

class BitcoinerManager implements FactoryContract
{
	
	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;

	/**
	 * The array of resolved bitcoiner drivers.
	 *
	 * @var array
	 */
	protected $bitcoiners = [];

	/**
	 * The registered custom driver creators.
	 *
	 * @var array
	 */
	protected $customCreators = [];
	
	/**
	 * Create a new bitcoiner manager instance.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return void
	 */
	public function __construct($app) {

		$this->app = $app;

	}

	/**
	 * Get a bitcoiner instance.
	 *
	 * @param  string  $name
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	public function drive($name = null) {

		return $this->bitcoiner($name);

	}

	/**
	 * Get a bitcoiner instance.
	 *
	 * @param  string  $name
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	public function bitcoiner($name = null) {

		$name = $name ?: $this->getDefaultDriver();

		return $this->bitcoiners[$name] = $this->get($name);

	}

	/**
	 * Attempt to get the bitcoiner.
	 *
	 * @param  string  $name
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	protected function get($name) {

		return isset($this->bitcoiners[$name]) ? $this->bitcoiners[$name] : $this->resolve($name);

	}

	/**
	 * Resolve the given bitcoiner.
	 *
	 * @param  string  $name
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function resolve($name) {

		$config = $this->getConfig($name);

		if (isset($this->customCreators[$config['driver']])) 
		{
			return $this->callCustomCreator($config);
		}

		$driverMethod = 'create'.ucfirst($config['driver']).'Driver';

		if (method_exists($this, $driverMethod)) 
		{
			return $this->{$driverMethod}($config);
		} else {
			throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
		}

	}

	/**
	 * Call a custom driver creator.
	 *
	 * @param  array  $config
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	protected function callCustomCreator(array $config) {

		$driver = $this->customCreators[$config['driver']]($this->app, $config);

		if ($driver instanceof BitcoinerContract) 
		{
			return $this->adapt($driver);
		}

		return $driver;

	}

	/**
	 * Create an instance of the bitcoin driver.
	 *
	 * @param  array  $config
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	public function createBitcoinDriver(array $config) {

		return $this->adapt(

			new BitcoinerAdapter(new BitcoinerClient($config))

		);

	}
	
	/**
	 * Create an instance of the bitcoin driver.
	 *
	 * @param  array  $config
	 * @return \Hht\Bitcoin\Bitcoiner\Bitcoiner
	 */
	public function createEasyblockchainDriver(array $config) {

		return $this->adapt(

			new BitcoinerAdapter(new BitcoinerClient($config))

		);

	}

	/**
	 * Adapt the bitcoiner implementation.
	 *
	 * @param  \Hht\Bitcoin\BitcoinerAdapter  $bitcoiner
	 * @return \Hht\Bitcoin\Contracts\Bitcoiner
	 */
	protected function adapt(BitcoinerAdapter $adapter) {

		return new Bitcoiner($adapter);

	}

	/**
	 * Set the given bitcoiner instance.
	 *
	 * @param  string  $name
	 * @param  mixed  $bitcoiner
	 * @return void
	 */
	public function set($name, $bitcoiner) {

		$this->bitcoiners[$name] = $bitcoiner;

	}

	/**
	 * Get the bitcoiner connection configuration.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function getConfig($name) {

		return $this->app['config']["bitcoiners.coiners.{$name}"];

	}

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver() {

		return $this->app['config']['bitcoiners.default'];

	}

	/**
	 * Register a custom driver creator Closure.
	 *
	 * @param  string	$driver
	 * @param  \Closure  $callback
	 * @return $this
	 */
	public function extend($driver, Closure $callback) {

		$this->customCreators[$driver] = $callback;

		return $this;

	}

	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {

		return $this->bitcoiner()->$method(...$parameters);

	}

}
