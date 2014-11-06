<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

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
		"hashtag"			=> "roleplaying"				// The hashtag to use for filtering purposes
	,	"categories"		=> array("people", "articles")	// The categories that your widget will show
	,	"view_count"		=> 250							// An optional setting; number of times it will be shown
	,	"number_slots"		=> 3							// The number of content slots to return (generally 2 or 3)
	);
	
	// Connect to the API and pull the response
	$apiData = Connect::to("sync_widget", "FeaturedWidgetAPI", $packet);
	
	
[ Possible Responses ]
	???

*/

class FeaturedWidgetAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "fast";		// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 20;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): array <str, mixed>					// RETURNS <str:mixed>
	
	// $this->runAPI()
	{
		// Make sure the proper data was sent
		if(!isset($this->data['hashtag']))
		{
			return array();
		}
		
		// Prepare Values
		$hashtag = Sanitize::variable($this->data['hashtag']);
		$slots = isset($this->data['number_slots']) ? (int) $this->data['number_slots'] : 2;
		$pullCount = (isset($this->data['view_count'])) ? (int) $this->data['view_count'] : 100;
		$categories = isset($this->data['categories']) ? $this->data['categories'] : array();
		
		shuffle($categories);
		
		// Loop through the categories
		foreach($categories as $category)
		{
			// Retrieve content based on the parameters provided
			if(!$verb = AppFeatured::getRandomVerb($hashtag, $category))
			{
				continue;
			}
			
			// Pull the Widget Data
			if(!$widgetData = AppFeatured::pull($hashtag, $category, $verb, $slots))
			{
				continue;
			}
			
			// Update the view count for the entries being pulled
			AppFeatured::updateViews($widgetData, $pullCount);
			
			// Return the content
			return array("category" => $category, "verb" => $verb, "widgetData" => $widgetData);
		}
		
		return array();
	}
	
}