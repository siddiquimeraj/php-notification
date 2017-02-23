<?php
namespace NFLib\Notification;
use NFLib\Authentication\Authenticate;
use NFLib\Authentication\Request;
use NFLib\Notification\NotifyException;

class Project {

	/**
	 * Get List of all projects by the client
	 */
	public static function projectList() {
		if(!Authenticate::isAuthorized()) {
       		 throw new NotifyException("You are not authorized for this method", 1);
       	}
        else {
			$api_end_point = "project";
			$request_data=[];
			return Request::sendRequest($api_end_point, $request_data);
		}
	}

	/**
	 * Get project details of project assigned
	 */
	public static function details($project_id) {
		if(!Authenticate::isAuthorized()){
       		 throw new NotifyException("You are not authorized for this method", 1);
       	}
        else {
			$request_data = ['id' => $project_id];
			$api_end_point = "project/detail";
			return Request::sendRequest($api_end_point, $request_data);
		}
	}
}


