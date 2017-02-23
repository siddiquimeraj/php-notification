<?php

require_once '../vendor/autoload.php'; 

use NFLib\Authentication\Authenticate;
use NFLib\Notification\Notification;
use NFLib\Notification\Project;
use NFLib\Notification\Subscriber;
use NFLib\Notification\Campaign;
use NFLib\Notification\Segment;

/**
 * API credentials issued by notifyfox.com .. Register to get yours.
 * @var array
 */
$api_credentials = [
	"user_id" 		=> "youremail@domain.com",
	"password" 		=> "yourpassword",
	"client_id" 	=> "Your CLIENT ID",
	"client_secret" => "Your CLIENT SECRET"
];
/**
 * Authenticate with the api credentials
 */
Authenticate::auth($api_credentials);

/**
 * Get subscriber details
 */
$subscriber_id = "582b03c52a5db434691aedb0";
$subscriber_detail = Subscriber::detail($subscriber_id);

/**
 * Options with subscriber identity
 * @var 	$options  	options to perform with subscriber
 *       				set
 *       				get
 *       				check
 *       				unset
 */
$subscriber_identity = Subscriber::identity($option, $userid, $subscriber_id);

/**
 * Getting all your campaign details
 */
$campaign = Campaign::details();

/**
 * Getting Project Detsils by project id
 * @var string
 */
$project_id = "5804bb80777336120a688572";
$project_list = Project::projectList();
$project_details  = Project::details($project_id);

/**
 * Getting segments details
 */
$segment = Segment::details();

/**
 * Sending Notification to subscribers
 */
$notification = new Notification();
$notification_msg = [
				"title"=>"This would be a title",
				"message"=>"This will be the notification message to be displayed",
				"image"=>"The image to show on the notification",
				"link"=> "A link where to redirect"
			];

$notification->setCondition("usid", "include", array("582b03c52a5db434691aedb0"));
$notification->setCondition("schedule", ["datetime"=>"", "timezone"=>""]);
$notification->setCondition("userid", "");
$notification->setCondition("geography", "include", ["country"=>["India", "Japan", "China"]]);
$notify_now = $notification->send($notification_msg);
