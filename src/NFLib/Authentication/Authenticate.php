<?php

namespace NFLib\Authentication;
use NFLib\Authentication\Request;

class Authenticate {
  protected static $user_id;

  protected static $password;

  protected static $client_id;

  protected static $client_secret;

  public static $access_token;

  protected static $refresh_token;

  protected static $token_lifetime;

  protected static $token_type;

  protected static $token_created_time;

  public static $tmp_directory="/var/www/html/php-notification/src/NFLib/tmp/";

  /**
   * Sets credentials sent in form of an array.
   */

  public function auth($credentials) {
    self::$user_id = $credentials["user_id"];
    self::$password = $credentials["password"];
    self::$client_id = $credentials["client_id"];
    self::$client_secret = $credentials["client_secret"];
    self::validateAccessToken();
  }

  
  /**
   * Returns whether the current instance is authorized or not.
   */
  public function isAuthorized() {
      return (self::validateAccessToken()) ? true : false;
  }

  /**
   * Sends an HTTP POST Request to get access token from API server.
   */
  protected static function requestAccessToken() {
    $post_data = [
                'grant_type' => 'password',
                'client_id' => self::$client_id,
                'client_secret' =>self::$client_secret,
                'username' => self::$user_id,
                'password' => self::$password  
            ];
    $api_end_point = "api/oauth/token";
    $result= Request::sendRequest($api_end_point, $post_data);
    self::saveTokens($result);
  }

  /**
   * Validates access token for a being alpha-numeric, not 0 length string and
   * that token lifetime has not exceeded current time.
   */
  protected static  function validateAccessToken() {
      self::loadTokens();
      if(strlen(self::$access_token)>0) {
        $time = time()- self::$token_created_time;
          if($time<self::$token_lifetime) {
            return true;
          } else {
              self::refreshToken();
          }  
      } else {
        throw new NotifyException("Error Processing Request", 1);
        
        return false;
      }
  }

  /**
   * Sends an HTTP POST request to get refreshed access token.
   */
  protected static function refreshToken() {
      $api_end_point = "api/oauth/token";
      $post_data=[
       'grant_type' => "refresh_token",
       'client_id' => self::$client_id, 
       'client_secret' => self::$client_secret, 
       'refresh_token' => self::$refresh_token
       ];
      $result= Request::sendRequest($api_end_point, $post_data);
      self::saveTokens($result);
  }



  /**
   * Load tokens where they were saved, database or file system, for reusing them.
   */
  protected static function loadTokens(){
      //Load tokens from file and check  current time save into array
      $token_file = self::$tmp_directory.'tokens.txt';
      if(file_exists($token_file)){
          $token_file = fopen($token_file, 'r');
          $contents = stream_get_contents($token_file);
          fclose($token_file);
          //Accessing contents and comparing Time
          $data = array();
          foreach(preg_split("/((\r?\n)|(\r\n?))/", $contents) as $line){
              if(!empty($line)){
                $line = explode(":", $line);
                $data[trim($line[0])] = trim($line[1]);
              }
          }
          self::$token_created_time = $data['Created'];
          self::$access_token = $data['access_token'];
          self::$refresh_token = $data['refresh_token'];
          self::$token_lifetime = $data['expires_in'];

      }
      else {
          self::requestAccessToken();
          self::loadTokens();
      }
  }
    /**
  * Dumps tokens to temp file after encoding. So that at next request we
  * read from the same file and use old access tokens
  */
  protected function saveTokens($tokens){
    if(!is_dir(self::$tmp_directory)){
      mkdir(self::$tmp_directory);
      }
      //create a  file to save tokens as well as insert created time
      $created_time= time();
      $token_file = fopen(self::$tmp_directory.'tokens.txt','w');
      fwrite($token_file, "Created : ".$created_time."\n");
      foreach ($tokens as $key => $value) {
      fwrite($token_file, "{$key} : {$value} \n");
    }
    fclose($token_file);
  }

    public function error_print($e, $catchMethod) {
    $error = substr($e->getMessage(), strpos($e->getMessage(), "response:")+9);
    echo $error."\n";
    self::createLog($catchMethod." - ".$e->getMessage());
  }

  /**
  * Writing log Files.
  */
  public  function createLog($log_message) {
    if(!self::$tmp_directory) {
      mkdir(self::$tmp_directory);
    }
    $log_message = (is_string($log_message)) ? $log_message : $log_message->message;
    $log_time = str_replace(' ', ':', date("h:i:s d-m-Y"));
    $log_file = fopen(self::$tmp_directory.'log.txt','a+');
    fwrite($log_file, $log_time."  :   ".$log_message."\n");
    fclose($log_file);
  }
}

?>
