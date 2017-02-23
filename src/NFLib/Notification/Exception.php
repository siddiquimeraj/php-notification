<?php

namespace NFLib\Notification;

class NotifyException extends Exception {

	public $error;
	/**
	 * Construct to handle error NotifyException
	 */
	function __construct($e) {
		var_dump($e);
	}

}