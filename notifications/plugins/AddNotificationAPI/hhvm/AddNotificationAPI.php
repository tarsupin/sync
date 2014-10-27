<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows a notification to be added to a user's notification list.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the packet with details about the notification
	$packet = array("uni_id" => $uniID, "url" => $url, "message" => $message);
	
	// Alternatively, we can send a packet with multiple notifications
	// Prepare the packet with multiple users and notification details
	$packet = array("uni_id_list" => $uniIDList, "url" => $url, "message" => $message);
	
	// Run the API
	Connect::to("sync_notifications", "AddNotificationAPI", $packet);
	
*/

class AddNotificationAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information is received
		if(!isset($this->data['url']) or !isset($this->data['message']))
		{
			return false;
		}
		
		// Update a single user with the notification
		if(isset($this->data['uni_id']))
		{
			return AppNotifications::add((int) $this->data['uni_id'], $this->data['url'], $this->data['message']);
		}
		
		// Update multiple users with the notification
		else if(isset($this->data['uni_id_list']))
		{
			return AppNotifications::addMultiple($this->data['uni_id_list'], $this->data['url'], $this->data['message']);
		}
		
		return false;
	}
}