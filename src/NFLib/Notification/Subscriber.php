<?php
namespace NFLib\Notification;
use NFLib\Notification\NotifyException;
use NFLib\Authentication\Request;
use NFLib\Authentication\Authenticate;

class Subscriber {
    /**
     *Subscriber Detail
     */
    public static function detail($subscriber_id) {
        if(!Authenticate::isAuthorized()) {
            throw new NotifyException("You are not authorized for this method", 1);
        }
        else {
              $api_end_point = "subscriber/detail";
              $request_data = ["id" => $subscriber_id];
              try {
                    return Request::sendRequest($api_end_point, $request_data);
                }
                catch (NotifyException $e) {
                  throw new NotifyException("Error in Subscriber detail"); 
              }
        }
       
    }
    /**
     *Subscriber Identification
     */
    public static function identity($type, $userid, $usid="") {
        if(!Authenticate::isAuthorized()) {
            throw new NotifyException("You are not authorized for this method", 1);
        }
        else {
          $api_end_point = "subscriber/user";
          // move to Subscriber Class chage method name to identity.
          $request_data = ($type=="set") ? ["type" => $type, "userid" => $userid, "usid"=>$usid] :["type" => $type, "userid" => $userid];
          return Request::sendRequest($api_end_point, $request_data);
        }
    }
}