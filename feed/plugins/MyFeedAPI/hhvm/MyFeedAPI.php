<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows sites to pull feeds for a particular user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the packet
	$packet = array(
		"uni_id"			=> $uniID			// The UniID to send a feed to.
	,	"page"				=> $page			// The page to return.
	,	"num_results"		=> $numResults		// The number of results to return.
	);
	
	$feedData = Connect::to("sync_feed", "MyFeedAPI", $packet);
*/

class MyFeedAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 10;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): array					// RETURNS <array>
	
	// $this->runAPI()
	{
		// Make sure the appropriate information was sent
		if(!isset($this->data['uni_id']))
		{
			return array();
		}
		
		// Prepare Values
		$uniID = (int) $this->data['uni_id'];
		$page = isset($this->data['page']) ? (int) $this->data['page'] : 1;
		$numResults = isset($this->data['num_results']) ? (int) $this->data['num_results'] : 15;
		
		// Update the feed once in a while
		UserFeed::updateFeed($uniID);
		
		// Get the User's Feed Posts
		$contentIDs = UserFeed::getFeedIDs($uniID);
		
		// Pull the necessary feed data
		$feedData = UserFeed::scanFeed($contentIDs);
		
		return $feedData;
	}
	
}