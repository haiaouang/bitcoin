<?php namespace Hht\Bitcoin;

use Illuminate\Support\Arr;
use Hht\Bitcoin\Core\Bitcoin;

class BitcoinerClient implements BitcoinerInterface
{

	/**
	 * IP address.
	 *
	 * @var string
	 */
	protected $host;
	
	/**
	 * Port address.
	 *
	 * @var string
	 */
	protected $port;
	
	/**
	 * User name.
	 *
	 * @var string
	 */
	protected $user;
	
	/**
	 * Password.
	 *
	 * @var string
	 */
	protected $password;
	
	/**
     * Bitcoin class.
     *
     * @var \Hht\Bitcoin\Core\Bitcoin
     */
	protected $bitcoin;
	
	/**
     * Create a new bitcoiner client.
     *
     * @param  array $config
     * @return void
     */
	public function __construct($config) {

		$keys = ['host', 'port', 'user', 'password'];
		
		foreach ($keys as $key)
		{
			$this->$key = Arr::get($config, $key);
		}

		$this->bitcoin = new Bitcoin($this->user, $this->password, $this->host, $this->port);

	}
	
	/**
     * Get http response code.
     *
     * @return int
     */
	public function getStatus() {
		
		return $this->bitcoin->status;

	}
	
	/**
     * Get http response error.
     *
     * @return string
     */
	public function getError() {

		return $this->bitcoin->error;

	}
	
	/**
     * Get http response content.
     *
     * @return string
     */
	public function getResponse() {
		
		return $this->bitcoin->response;

	}
	
	/**
     * Get http raw response content.
     *
     * @return string
     */
	public function getRawResponse() {
		
		return $this->bitcoin->raw_response;

	}
	
	/**
	 * Dynamically call bitcoin.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {

		return call_user_func_array([$this->bitcoin, $method], $parameters);

	}

}