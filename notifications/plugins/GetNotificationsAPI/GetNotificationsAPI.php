<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to retrieve a list of notifications of a user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id"		=> $uniID
	,	"page"			=> $page			// Defaults to 1
	,	"return_num"	=> $returnNum		// Defaults to 5
	);
	
	// Connect to this API from UniFaction
	$notifications = Connect::to("sync_notifications", "GetNotificationsAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

$notifications = array(
	array("url" => "...", "message" => "...", "date_created" => "...")
,	array("url" => "...", "message" => "...", "date_created" => "...")
);

*/

class GetNotificationsAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <int:[str:mixed]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']))
		{
			return false;
		}
		
		// Prepare Values
		$page = isset($this->data['page']) ? (int) $this->data['page'] : 1;
		$returnNum = isset($this->data['return_num']) ? (int) $this->data['return_num'] : 5;
		
		// Get the list of notifications for the user
		return AppNotifications::get((int) $this->data['uni_id'], $page, $returnNum);
	}
	
	
}
