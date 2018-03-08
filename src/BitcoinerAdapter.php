<?php namespace Hht\Bitcoin;

class BitcoinerAdapter
{
	/**
     * The bitcoiner instance.
     *
     * @var \Hht\Bitcoin\Bitcoiner\BitcoinerInterface
     */
    protected $driver;

	/**
     * Create a new bitcoiner adapter instance.
     *
     * @param  \Hht\Bitcoin\Bitcoiner\BitcoinerInterface  $driver
     * @return void
     */
	public function __construct(BitcoinerInterface $driver) {

        $this->driver = $driver;

    }

	/**
     * Get a bitcoiner instance.
     *
     * @return \Hht\Bitcoin\Bitcoiner\BitcoinerInterface
     */
    public function getDriver() {

        return $this->driver;

    }
	
	/**
     * Pass dynamic methods call onto bitcoiner.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, array $parameters) {

        return call_user_func_array([$this->driver, $method], $parameters);

    }
}