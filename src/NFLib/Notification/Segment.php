<?php
namespace NFLib\Notification;
use NFLib\Authentication\Authenticate;
use NFLib\Authentication\Request;
use NFLib\Notification\NotifyException;

class Segment {
	/**
	 * Get segments details of the client
	 */
	public static function details($segment="") {	
		if(!Authenticate::isAuthorized()) {
       		 throw new NotifyException("You are not authorized for this method", 1);
       	}
        else {
			$api_end_point = "subscriber/segment";
			$request_data = ["seg_name" => $segment];
			return Request::sendRequest($api_end_point, $request_data);
		}
	}
}


