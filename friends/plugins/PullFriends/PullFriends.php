<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This is the Pull Synchronization API for Friends.

This API is called by the Uni-Sites to retrieve any friend updates that UniFaction has announced.


------------------------------
------ Calling this API ------
------------------------------

This API deletes any updates that the Uni-Site has requested.
	
Losing friend updates is not desirable, so moderate integrity testing is in place. It is performed on UniFaction (pushes must work for rows to be deleted), and deletion on the sync site only occurs if it knows the response is valid. However, it is still possible that a response could get disconnected and cause de-syncing. Fortunately, friends are re-synced on any update to the UniID/FriendID connection. If issues occur, re-syncing a user on a site directly may be an option.
	
	// Set the API to use the Post Method
	$settings = array(
		"post"		=> true
	);
	
	// Connect to this API from a Uni-Site
	$syncData = Connect::to("sync_friends", "PullFriends", true, $settings);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

$packet = array(
	array($uniID, $friendID, $viewClearance, $interactClearance)
,	array($uniID, $friendID, $viewClearance, $interactClearance)
,	array($uniID, $friendID, $viewClearance, $interactClearance)
);

*/

class PullFriends extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <int:[int:int]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		$packet = array();
		
		// Gather a list of up to 1000 updates
		Database::startTransaction();
		
		if($results = Database::selectMultiple("SELECT * FROM sync_friends WHERE site_handle=? LIMIT 1000", array($this->apiHandle)))
		{
			Database::query("DELETE FROM sync_friends WHERE site_handle=? LIMIT 1000", array($this->apiHandle));
		}
		
		Database::endTransaction();
		
		// Cycle through the list of friend updates provided and add them to the response packet
		foreach($results as $note)
		{
			$packet[] = array((int) $note['uni_id'], (int) $note['friend_id'], (int) $note['view_clearance'], (int) $note['interact_clearance']);
		}
		
		return $packet;
	}
	
	
}
