<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows friends to be removed from the appropriate lists.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the packet with details about the friends to remove
	$packet = array(
		"uni_id" => $uniID
	,	"friend_id" => $friendID
	);
	
	// Run the API
	Connect::to("sync_friends", "RemoveFriendAPI", $packet);
	
*/

class RemoveFriendAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 8;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information is received
		if(!isset($this->data['uni_id']) or !isset($this->data['friend_id']))
		{
			return false;
		}
		
		// Remove the friends
		AppFriends::remove($this->data['uni_id'], $this->data['friend_id']);
	}
}