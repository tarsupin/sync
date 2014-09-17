<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows other sites to connect and pull the "Featured Content" widget.

The way this widget works is that it shows off featured content that we (the admins of UniFaction) have curated. This content fits into one of several categories, such as: "articles", "people", "sites", "hashtags", "links", "tools", "games", etc.

Within these categories, we might search for a top-level hashtag (such as "nfl", "roleplaying", etc) - the same hashtags that have sites by the same name association.

You can send details of what you're looking for (or allow) to the API. For example, you may request "people" categories only, or may want to switch between "articles" and "sites". You can also specify the hashtag to sort by.

When this content is being loaded, it will apply a "verb". The system will automate this in most cases. It must also fit into a particular "verb", such as: "useful", "interesting", "fun", etc.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the API Packet
	$packet = array(
		"hashtag"			=> "roleplaying"				// The top-tier hashtag to use for filtering purposes
	,	"categories"		=> array("people", "articles")	// The categories that your widget will show
	,	"view_count"		=> 100							// An optional setting; number of times it will be shown
	,	"number_slots"		=> 3							// The number of content slots to return (generally 2 or 3)
	);
	
	// Connect to the API and pull the response
	$apiData = Connect::to("sync_widget", "FeaturedWidgetAPI", $packet);
	
	
[ Possible Responses ]
	???

*/

class FeaturedWidgetAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 20;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <str:mixed>
	
	// $this->runAPI()
	{
		// Make sure the proper data was sent
		if(!isset($this->data['categories']) or !isset($this->data['hashtag']))
		{
			return array();
		}
		
		// Prepare Values
		$slots = isset($this->data['number_slots']) ? (int) $this->data['number_slots'] : 2;
		$cat = $this->data['categories'][mt_rand(0, count($this->data['categories']) - 1)];
		
		// Retrieve content based on the parameters provided
		if(!$verb = Database::selectValue("SELECT DISTINCT verb FROM widget_featured WHERE hashtag=? AND category=? ORDER BY RAND() LIMIT 1", array($this->data['hashtag'], $cat)))
		{
			return array();
		}
		
		// Pull the necessary data
		if($widgetData = Database::selectMultiple("SELECT id, hashtag, category, verb, title, description, url FROM widget_featured WHERE hashtag=? AND category=? AND verb=? ORDER BY RAND() LIMIT " . $slots, array($this->data['hashtag'], $cat, $verb)))
		{
			// Update the values
			$pullCount = (isset($this->data['view_count'])) ? (int) $this->data['view_count'] : 100;
			
			foreach($widgetData as $wData)
			{
				Database::query("UPDATE widget_featured SET views=views+? WHERE id=? LIMIT 1", array($pullCount, $wData['id']));
			}
		}
		
		// Return the content
		return array("category" => $cat, "verb" => $verb, "widgetData" => $widgetData);
	}
	
}
