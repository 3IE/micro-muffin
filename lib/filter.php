<?php

namespace Lib;

class Filter
{
	public $name;
	public $callback;

	function __construct($name, $callback)
	{
		$this->name = $name;
		$this->callback = $callback;
	}

	public function exec()
	{
		$result = $this->callback();
		return $result;
	}

	public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
    }
}