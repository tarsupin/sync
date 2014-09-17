<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This is the Push Synchronization API for Notifications.

This API is called by uni-sites that want to submit notifications to UniFaction.


------------------------------
------ Calling this API ------
------------------------------

Uni-Sites want to inform UniFaction of the alerts they provide. To do so, they need to prepare a packet that includes the full details about all of the relevant notifications they are submitting.
	
	// Prepare the Packet with list of notifications
	$packet = array(
		array($uniID, $senderID, $message, $url, $dateCreated, [$uniIDList])
	,	array($uniID, $senderID, $message, $url, $dateCreated, [$uniIDList])
	,	array($uniID, $senderID, $message, $url, $dateCreated, [$uniIDList])
	);
	
	// Set the API to use the Post Method
	$settings = array(
		"post"		=> true
	);
	
	$syncData = Connect::to("sync_notifications", "PushNotifications", $packet, $settings);
	
*/

class PushNotifications extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!is_array($this->data) or count($this->data) == 0)
		{
			return false;
		}
		
		// Cycle through the list of notifications sent
		Database::startTransaction();
		
		foreach($this->data as $note)
		{
			Database::query("INSERT INTO sync_notifications (site_handle, uni_id, sender_id, message, url, date_created) VALUES (?, ?, ?, ?, ?, ?)", array($this->apiHandle, $note[0], $note[1], $note[2], $note[3], $note[4]));
		}
		
		Database::endTransaction();
		
		return true;
	}
	
	
}
