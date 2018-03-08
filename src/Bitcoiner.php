<?php namespace Hht\Bitcoin;

use Illuminate\Support\Arr;
use Hht\Bitcoin\Core\Bitcoin;

class Bitcoiner
{
	
	/**
     * @var BitcoinerAdapter
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param BitcoinerAdapter $adapter
     */
    public function __construct(BitcoinerAdapter $adapter) {

        $this->adapter = $adapter;

    }


    /**
     * Get the Adapter.
     *
     * @return BitcoinerAdapter adapter
     */
    public function getAdapter() {

        return $this->adapter;

    }

	/**
     * Pass dynamic methods call onto BitcoinerAdapter.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, array $parameters) {

        return call_user_func_array([$this->adapter, $method], $parameters);

    }

}