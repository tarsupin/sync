<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This is the Pull Synchronization API for Notifications.

This API is called by UniFaction to retrieve a list of notifications that other uni-sites have submitted.


------------------------------
------ Calling this API ------
------------------------------

To help mitigate any synchronization issues, this API deletes any notifications that UniFaction has already received.
	
Losing notifications is not a severe enough complication to warrant integrity testing for delivery. Therefore, to reduce overhead, UniFaction does not verify whether or not the results were received, nor does it need any sync timestamp tests.
	
	// Set the API to use the Post Method
	$settings = array(
		"post"		=> true
	);
	
	// Connect to this API from UniFaction
	$syncData = Connect::to("sync_notifications", "PullNotifications", true, $settings);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

$packet = array(
	array($siteHandle, $uniID, $senderID, $message, $url, $dateCreated)
,	array($siteHandle, $uniID, $senderID, $message, $url, $dateCreated)
,	array($siteHandle, $uniID, $senderID, $message, $url, $dateCreated)
);

*/

class PullNotifications extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array("auth");		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 100;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <int:[int:mixed]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		$packet = array();
		
		// Gather a list of up to 1000 notifications
		Database::startTransaction();
		
		$results = Database::selectMultiple("SELECT * FROM sync_notifications LIMIT 1000", array());
		Database::query("DELETE FROM sync_notifications LIMIT 1000", array());
		
		Database::endTransaction();
		
		// Cycle through the list of notifications provided and add them to the response packet
		foreach($results as $note)
		{
			$packet[] = array($note['note_type'], (int) $note['uni_id'], (int) $note['sender_id'], $note['message'], $note['url'], (int) $note['date_created']);
		}
		
		return $packet;
	}
	
	
}
