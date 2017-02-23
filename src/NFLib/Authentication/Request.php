<?php

namespace NFLib\Authentication;
use NFLib\Notification\NotifyException;
use GuzzleHttp\Client;
use GuzzleHttp\NotifyException\RequestException;
use GuzzleHttp\NotifyException\ClientException;
use GuzzleHttp\Middleware;
use NFLib\Authentication\Authenticate;
/**
* Handling All the Request using API
*/
class Request {

	protected static $base_uri="https://notify.rankwatch.com:1335";

	protected static $api_end_point;

	protected static $payload;

	protected static $action;

	public static $request_result;

	protected static $access_token;


	public static function send($end_point, $payload, $access_token="") {
		self::$api_end_point = $end_point;
		self::$payload = $payload;
		self::$access_token = $access_token;
		self::$request_result = self::MakeRequest();
		return self::$request_result;
	}

	public static function MakeRequest() {
		$api = explode('/', self::$api_end_point);
		$data_sending_method = ($api[count($api)-1]=="send") ? "json" : "form_params";
		self::$api_end_point = (self::$action=="request") ? self::$api_end_point : self::$api_end_point."?access_token=".self::$access_token;

		$client = new Client([
		// Base URI is used with relative requests
		'base_uri' => self::$base_uri,
		// You can set any number of default request options.
		'timeout'  => 2.0,
		//SSL Verification if have ssl set to true
		'verify'   => false
		]);
		try{

			//Authenticate using 
			$response = $client->request('POST', self::$api_end_point, [
			    $data_sending_method => self::$payload,
			]);
			//Status code 
			$http_status_code = $response->getStatusCode();
			//Headers
			$content_length = $response->getHeader('1000');
			//body
			$content_body = $response->getBody()->getContents();

			$body= (array) json_decode($content_body);
			    if(isset($body['status']) && $body['status']=='error'){
			      //var_dump($body['errmsg']);
			      new NotifyException($body['errmsg']);
			      $this->createLog($body['errmsg']);
			    }
			    else {
			      return $body;
			    }

		} catch(NotifyException $e) { 
			  throw new NotifyException($e);
			  Authenticate::error_print($e, "NotifyException");

		} catch(ClientException $e){
		  	Authenticate::error_print($e, "ClientException");

		} catch(RequesException $e){
		  	Authenticate::error_print($e, "RequestException");
		}
	}


	public function sendRequest($api, $data="") {
	   return  self::send($api, $data, Authenticate::$access_token);
  	}
}
	?>