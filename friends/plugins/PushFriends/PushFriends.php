<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This is the Push Synchronization API for Friends.

This API is called by UniFaction to submit the friend updates that have changed.


------------------------------
------ Calling this API ------
------------------------------

When friends are added, removed, or updated on UniFaction, the other Uni-Sites need to be informed of these changes. UniFaction must submit it's friend changes to this sync site. To do so, it must prepare a packet that includes the full details of the friend updates being submitting.
	
	// Prepare the packet of friend updates
	$packet = array(
		array($siteHandle, $uniID, $friendID, $viewClearance, $interactClearance)
	,	array($siteHandle, $uniID, $friendID, $viewClearance, $interactClearance)
	,	array($siteHandle, $uniID, $friendID, $viewClearance, $interactClearance)
	);
	
	// Set the API to use the Post Method
	$settings = array(
		"post"		=> true
	);
	
	$syncData = Connect::to("sync_friends", "PushFriends", $packet, $settings);
	
*/

class PushFriends extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array("auth");		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 8;			// <int> The clearance level required to use this API.
	
	
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
		
		// Cycle through the list of friend entries sent and insert them into the system
		Database::startTransaction();
		
		foreach($this->data as $note)
		{
			Database::query("REPLACE INTO sync_friends (site_handle, uni_id, friend_id, view_clearance, interact_clearance) VALUES (?, ?, ?, ?, ?)", array($note[0], $note[1], $note[2], $note[3], $note[4]));
		}
		
		Database::endTransaction();
		
		return true;
	}
	
	
}
