<?php

namespace NFLib\Notification;
use NFLib\Notification\NotifyException;
use NFLib\Authentication\Request;
use NFLib\Authentication\Authenticate;

class Notification {
  
    protected $auth;
    protected $payload;
    protected $conditions = [];
    protected $conditions_list = ["geography", "technology", "usid", "segments", "schedule", "userid"];

    /**
     * Sends push notification
     */
    public function send($request_data) {
        $this->conditions["message"] = $request_data;
        $api_end_point = "notification/send";
        if(Authenticate::isAuthorized()) {
            return Request::sendRequest($api_end_point, $this->buildPayload());
        }
        else {
            throw new NotifyException("Error Processing Request", 1); 
        } 
    }

    /**
     * addConditions
     */
    public function setCondition($condition_name, $condition_type, $condition_value=[]) {
      $args = count(func_get_args());
        //Check if $condition name is in desired condition list
        if(in_array($condition_name, $this->conditions_list)) {
            if($args==3){
              $this->conditions[$condition_name][$condition_type] = $condition_value;
            }
            else {
               $this->conditions[$condition_name] = $condition_type;
            }
            return $this->conditions;
        }
        else {
            throw new NotifyException("Provided Condition could not be found");
            
        }
    }


    /**
     * Get All Condition set by the user
     */
    protected function getCondition($condition) {
        return isset($this->conditions[$condition]) ? $this->conditions[$condition] : [];
    }

    /**
     * Build Payload to send Notification
     */
    protected function buildPayload() {
      $this->payload = [];
      $this->payload['userid'] = $this->getCondition('user_id');
      $this->payload['message'] = $this->getCondition('message');
      $this->payload['usid'] = $this->getCondition('usid');
      $this->payload['schedule'] = $this->getCondition('schedule');
      $this->payload['segments'] = $this->getCondition('segments');
      $this->payload['technology'] = $this->getCondition('technology');
      $this->payload['geography'] = $this->getCondition('geography');
      $this->payload['geography'] = (!empty($this->payload['geography'])) ? $this->modifyPayload($this->payload['geography']) : $this->payload['geography'];
      return $this->payload;
    }

    protected function modifyPayload($payload) {
        $key_handler = [
            "country" =>  "cntry_nm",
            "region"  =>  "regn_nm",
            "city"    =>  "cty_nm",
            "latitude"  =>  "lat",
            "longitude" =>  "lng"
        ];
        $geography_keys = array_keys($payload);
        for($i = 0; $i < count($geography_keys); $i++ ) {
            $location_keys = array_keys($payload[$geography_keys[$i]]);
            for( $j = 0; $j < count($location_keys); $j++ ) {
                $payload[$geography_keys[$i]][$key_handler[$location_keys[$j]]] = $payload[$geography_keys[$i]][$location_keys[$j]];
                unset($payload[$geography_keys[$i]][$location_keys[$j]]);
            }
        }
        return $payload;
    } 
}