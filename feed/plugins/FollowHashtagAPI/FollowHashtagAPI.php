<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows sites users to follow or un-follow hashtags from external sites.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the feed submission packet
	$packet = array(
		"uni_id"			=> $uniID		// The UniID of the person setting a feed tracker
	,	"hashtag"			=> $hashtag		// The hashtag to follow (or unfollow)
	,	"follow"			=> $follow		// TRUE if you're following, FALSE if you're unfollowing
	);
	
	// Submit the packet
	Connect::to("feed", "FollowHashtagAPI", $packet);
*/

class FollowHashtagAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 10;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['hashtag']))
		{
			return false;
		}
		
		// Prepare Values
		$uniID = (int) $this->data['uni_id'];
		$hashtag = Sanitize::variable($this->data['hashtag']);
		$follow = isset($this->data['follow']) ? (bool) $this->data['follow'] : true;
		
		// Follow the designated thing (if set to follow)
		if($follow)
		{
			return UserFeed::follow($uniID, $hashtag);
		}
		
		// Unfollow the designated thing
		return UserFeed::unfollow($uniID, $hashtag);
	}
	
}
