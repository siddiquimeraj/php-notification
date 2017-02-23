<?php
namespace NFLib\Notification;
use NFLib\Authentication\Authenticate;
use NFLib\Authentication\Request;
use NFLib\Notification\NotifyException;

class Campaign {
	/**
	 * Get Campaign Details
	 */
	public static  function details() {
		if(!Authenticate::isAuthorized()){
       		 throw new NotifyException("You are not authorized for this method", 1);
       	}
        else {
			$api_end_point = "subscriber/campaign/detail";
			$request_data = []; //Nothing
			return Request::sendRequest($api_end_point, $request_data);
		}
	}
}
