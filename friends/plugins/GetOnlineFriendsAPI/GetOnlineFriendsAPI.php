<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to retrieve a list of online friends of a user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array("uni_id" => $uniID);
	
	// Connect to this API from UniFaction
	$friends = Connect::to("sync_friends", "GetOnlineFriendsAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

$friends = array(
	array("uni_id" => $friendID, "handle" => $friendHandle, "display_name" => $friendName)
,	array("uni_id" => $friendID, "handle" => $friendHandle, "display_name" => $friendName)
);

*/

class GetOnlineFriendsAPI extends API {
	
	
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
		
		// Update My Activity
		AppFriends::updateActivity($this->data['uni_id']);
		
		// Get a list of your online friends
		return AppFriends::getActiveFriendList($this->data['uni_id']);
	}
	
}
