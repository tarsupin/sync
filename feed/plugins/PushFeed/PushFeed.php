<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows other sites to push content entries to this system that can be listed on user feeds.


------------------------------
------ Calling this API ------
------------------------------

	// Prepare the feed submission packet
	$packet = array(
		"site_handle"		=> $siteHandle		// The site that is providing the feed
	,	"author_handle"		=> $authorHandle	// The user's handle that posted the original content
	,	"url"				=> $url				// The URL source to access this content
	,	"title"				=> $title			// The title of the feed content
	,	"description"		=> $description		// The blurb associated with the feed to give a description about it
	,	"thumbnail"			=> $thumbnail		// The small image (mobile sized) to accompany this content feed
	,	"primary_hashtag"	=> $primeHashtag	// The primary hashtag associated with this feed.
	,	"hashtags"			=> $hashtagList		// The list of hashtags being submitted to the feed system
	);
	
	// Submit the packet
	Connect::to("feed", "PushFeed", $packet);
	
*/

class PushFeed extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();	// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 250;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['site_handle']) or !isset($this->data['author_id']) or !isset($this->data['url']) or !isset($this->data['title']) or !isset($this->data['description']) or !isset($this->data['primary_hashtag']) or !isset($this->data['hashtags']))
		{
			return false;
		}
		
		// Prepare Values
		$siteHandle = Sanitize::variable($this->data['site_handle']);
		$authorID = (int) $this->data['author_id'];
		$url = Sanitize::url($this->data['url']);
		$title = Sanitize::safeword($this->data['title'], "?");
		$description = Sanitize::safeword($this->data['description'], "?");
		$thumbnail = isset($this->data['thumbnail']) ? Sanitize::url($this->data['thumbnail']) : '';
		$primaryHashtag = Sanitize::variable($this->data['primary_hashtag']);
		$hashtagList = array();
		
		foreach($this->data['hashtags'] as $ht)
		{
			$hashtagList[] = Sanitize::variable($ht);
		}
		
		// Make sure the Author's UniID is registered on the site
		// If it doesn't exit, silently register them
		if(!User::get($authorID))
		{
			User::silentRegister($authorID);
		}
		
		// Create the Feed Entry
		$success = FeedCore::setEntry($siteHandle, $authorID, $url, $title, $description, $thumbnail, $primaryHashtag, $hashtagList);
		
		// If the feed entry was successfully created
		if($success and FeedCore::$feedID)
		{
			// Return the Feed ID that was applied
			$this->meta['feed_id'] = FeedCore::$feedID;
		}
		
		return $success;
	}
	
}
