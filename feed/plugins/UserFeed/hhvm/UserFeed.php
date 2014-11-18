<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the UserFeed Plugin ------
---------------------------------------

This plugin provides functionality for the user feed, such as enabling the user to retrieve the appropriate feeds. It also provides functionality for following and unfollowing hashtags.

-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class UserFeed {
	
	
/****** Plugin Variables ******/
	public static int $updateDuration = 180;		// <int> Duration between feed updates. Base 3 minutes.
	public static int $maxUpdateMicroSeconds = 700;	// <int> Number of microseconds to allow for an update; 0.7 seconds.
	public static int $loopCount = 1000;			// <int> The number of rows to search during an update loop.
	
	
/****** Follow a hashtag ******/
	public static function follow
	(
		int $uniID		// <int> The UniID to have follow a hashtag.
	,	string $hashtag	// <str> The hashtag that the user is being assigned to follow.
	): bool				// RETURNS <bool> TRUE if the hashtag was followed, FALSE if an error occurred.
	
	// UserFeed::follow($uniID, $hashtag);
	{
		return Database::query("REPLACE INTO `feed_following` (uni_id, hashtag) VALUES (?, ?)", array($uniID, $hashtag));
	}
	
	
/****** Unfollow a hashtag ******/
	public static function unfollow
	(
		int $uniID		// <int> The UniID to have unfollow a hashtag.
	,	string $hashtag	// <str> The hashtag that the user is unfollowing.
	): bool				// RETURNS <bool> TRUE if the feed was followed, FALSE if an error occurred.
	
	// UserFeed::unfollow($uniID, $hashtag);
	{
		return Database::query("DELETE IGNORE FROM `feed_following` (uni_id, hashtag) VALUES (?, ?)", array($uniID, $hashtag));
	}
	
	
/****** Get a list of hashtags that a user follows for a user ******/
	public static function getHashtags
	(
		int $uniID			// <int> The UniID to retrieve the list of hashtags from.
	): array <int, str>					// RETURNS <int:str> The hashtags the user is following.
	
	// $hashtags = UserFeed::getHashtags($uniID);
	{
		// Attempt to retrieve the list of things the user is following
		if(!$getData = Database::selectMultiple("SELECT hashtag FROM feed_following WHERE uni_id=?", array($uniID)))
		{
			return array();
		}
		
		// Prepare Values
		$hashtagList = array();
		
		// Cycle through the list of following data
		foreach($getData as $gData)
		{
			$hashtagList[$gData['hashtag']] = $gData['hashtag'];
		}
		
		return $hashtagList;
	}
	
	
/****** Get a list of hashtags in a STR:BOOL format ******/
	public static function getHashtagsBoolFormat
	(
		int $uniID			// <int> The UniID to retrieve the list of hashtags from.
	): array <str, bool>					// RETURNS <str:bool> The hashtags the user is following.
	
	// $hashtags = UserFeed::getHashtagsBoolFormat($uniID);
	{
		// Attempt to retrieve the list of things the user is following
		if(!$getData = Database::selectMultiple("SELECT hashtag FROM feed_following WHERE uni_id=?", array($uniID)))
		{
			return array();
		}
		
		// Prepare Values
		$hashtagList = array();
		
		// Cycle through the list of following data
		foreach($getData as $gData)
		{
			$hashtagList[$gData['hashtag']] = true;
		}
		
		return $hashtagList;
	}
	
	
/****** Update a user's feed ******/
	public static function updateFeed
	(
		int $uniID		// <int> The UniID of the feed to update.
	): bool				// RETURNS <bool> TRUE if you've updated the feed, FALSE if not.
	
	// UserFeed::updateFeed($uniID);
	{
		// Need to identify when the last feed update was made
		if(!$lastUpdate = Database::selectOne("SELECT last_feed_update, last_feed_id FROM users WHERE uni_id=? LIMIT 1", array($uniID)))
		{
			$lastUpdate = array('last_feed_update' => 0, 'last_feed_id' => 0);
		}
		
		// Only update the feed if you haven't done so recently
		if($lastUpdate['last_feed_update'] > time() - self::$updateDuration)
		{
			return false;
		}
		
		// Prepare Values
		$cycleTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
		$currentID = (int) $lastUpdate['last_feed_id'];
		$feedIDList = array();
		
		/*
			Note: I know the query below isn't perfect, and there are probably much faster ways to handle it, but
			for now this will get what we want done in a proper manner.
			
			Once we've had some growth and some time to contemplate a really good system for this, we can figure out
			how to improve upon this algorithm.
		*/
		
		// Get a list of the hashtags you follow, since we'll search each of those independently
		$htFollowed = self::getHashtagsBoolFormat($uniID);
		
		// Get self::$loopCount entries to search through for the most recent entries
		while(true)
		{
			$searchEntries = Database::selectMultiple("SELECT id, hashtags FROM feed_data WHERE id > ? LIMIT " . (self::$loopCount), array($currentID));
			
			// Loop through every row we gathered and test if we want to add it to our feed
			foreach($searchEntries as $ent)
			{
				$ent['id'] = (int) $ent['id'];
				$hashList = explode(" ", $ent['hashtags']);
				
				foreach($hashList as $ht)
				{
					if(isset($htFollowed[$ht]) and !isset($feedIDList[$ent['id']]))
					{
						$feedIDList[$ent['id']] = $ent['id'];
					}
				}
			}
			
			// Check how many rows we recovered from this most recent search
			$totalRows = count($searchEntries);
			
			// Update the next set of ID position
			$currentID += $totalRows;
			
			// End the loop if we found all of the entries
			if(count($searchEntries) < self::$loopCount) { break; }
			
			// End the loop if we ran out of time
			$curTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
			
			if($curTime > ($cycleTime + (self::$maxUpdateMicroSeconds / 1000))) { break; }
		}
		
		// Need to update your last feed ID
		Database::query("UPDATE users SET last_feed_update=?, last_feed_id=? WHERE uni_id=? LIMIT 1", array(time(), $currentID, $uniID));
		
		Database::startTransaction();
		
		foreach($feedIDList as $feedID)
		{
			Database::query("INSERT INTO feed_display (uni_id, feed_id) VALUES (?, ?)", array($uniID, $feedID));
		}
		
		return Database::endTransaction();
	}
	
	
/****** Prepare a page for handling a content feed ******/
	public static function prepare (
	): void							// RETURNS <void> runs the appropriate preparation methods.
	
	// UserFeed::prepare([$searchArchetype]);
	{
		// Prepare Header Handling
		Photo::prepareResponsivePage();
		
		Metadata::addHeader('<link rel="stylesheet" href="' . CDN . '/css/content-system.css" /><script src="' . CDN . '/scripts/content-system.js"></script>');
	}
	
	
/****** Retrieve a User's Feed Posts ******/
	public static function getFeedIDs
	(
		int $uniID			// <int> The UniID of whose feed to retrieve.
	,	int $page = 1		// <int> The page to retrieve from the feed.
	,	int $showNum = 15	// <int> The number of rows to retrieve.
	): void					// RETURNS <void> OUTPUTS the feed.
	
	// $contentIDs = UserFeed::getFeedIDs($uniID, [$page], [$showNum]);
	{
		$contentIDs = array();
		
		$getList = Database::selectMultiple("SELECT feed_id FROM feed_display WHERE uni_id=? ORDER BY feed_id DESC LIMIT " . (($page - 1) * $showNum) . ", " . ($showNum + 0), array($uniID));
		
		foreach($getList as $getID)
		{
			$contentIDs[] = (int) $getID['feed_id'];
		}
		
		return $contentIDs;
	}
	
	
/****** Display the Feed Header ******/
	public static function displayHeader
	(
		string $title			// <str> The header of this feed.
	,	string $backTitle = ""	// <str> The title of the previous page (breadcrumb).
	,	string $backURL = ""	// <str> The URL of the previous page (breadcrumb).
	): void					// RETURNS <void> runs the appropriate preparation methods.
	
	// UserFeed::displayHeader($title, $backTitle, $backURL);
	{
		echo '
		<div id="c-feed-head">';
		
		if($backURL)
		{
			echo '
			<div id="c-feed-head-tagcell"><div id="c-feed-navtag"><a href="' . $backURL . '">' . $backTitle . '</a></div></div>';
		}
		
		echo '
			<div id="c-feed-head-title"><h1>' . $title . '</h1></div>
		</div>';
	}
	
	
/****** Scan the content to retrieve core feed data ******/
	public static function scanFeed
	(
		array <int, int> $contentIDs		// <int:int> The array of content IDs to retrieve feed data for.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> the core data for the article.
	
	// $feedData = UserFeed::scanFeed($contentIDs);
	{
		// Prepare Values
		$feedData = array();
		
		list($sqlWhere, $sqlArray) = Database::sqlFilters(array("c.id" => $contentIDs));
		
		// Retrieve the Content Data
		$pullFeed = Database::selectMultiple("SELECT c.id, c.site_handle, c.author_id, c.url, c.primary_hashtag, c.hashtags, c.title, c.thumbnail, c.description, c.date_posted, u.handle, u.display_name FROM feed_data c LEFT JOIN users u ON c.author_id=u.uni_id WHERE " . $sqlWhere, $sqlArray);
		
		// Loop through each feed entry
		foreach($pullFeed as $scanData)
		{
			// Recognize Integers
			$scanData['id'] = (int) $scanData['id'];
			$scanData['author_id'] = (int) $scanData['author_id'];
			$scanData['uni_id'] = (int) $scanData['author_id'];
			$scanData['date_posted'] = (int) $scanData['date_posted'];
			
			// Add the entry to the final feed data
			$feedData[$scanData['id']] = $scanData;
		}
		
		// Return Feed Data
		return $feedData;
	}
	
	
/****** Output a content feed ******/
	public static function displayFeed
	(
		array <int, int> $contentIDs			// <int:int> The array that contains the content entry IDs for the feed.
	): void						// RETURNS <void> outputs the appropriate line.
	
	// UserFeed::displayFeed($contentIDs);
	{
		// Make sure Content IDs are available
		if(!$contentIDs)
		{
			echo "No articles available here at this time."; exit;
		}
		
		// Prepare Values
		$socialURL = URL::unifaction_social();
		$hashtagURL = URL::hashtag_unifaction_com();
		
		// Pull the necessary feed data
		$feedData = self::scanFeed($contentIDs);
		
		// Loop through the content entries in the feed
		// Looping with the $contentIDs variable allow us to maintain the proper ordering
		foreach($contentIDs as $contentID)
		{
			// Retrieve the feed data relevant to this particular entry (main title, description, image, etc)
			$coreData = $feedData[$contentID];
			
			// Display the Content
			echo '
			<hr class="c-hr" />
			<div class="c-feed-wrap">
				<div class="c-feed-left">';
			
			// If we have a thumbnail version of the image, use that one
			if($coreData['thumbnail'])
			{
				echo '<a href="' . $coreData['url'] . '">' . Photo::responsive($coreData['thumbnail'], "", 950, "", 950, "c-feed-img") . '</a>';
			}
			
			echo '
				</div>
				<div class="c-feed-right">
					<div class="c-feed-date feed-desktop">' . date("m/j/y", $coreData['date_posted']) . '</div>
					<div class="c-feed-title"><a href="' . $coreData['url'] . '">' . $coreData['title'] . '</a></div>
					<div class="c-feed-author feed-desktop">Written by <a href="' . $socialURL . '/' . $coreData['handle'] . '">' . $coreData['display_name'] . '</a> (<a href="' . $socialURL . '/' . $coreData['handle'] . '">@' . $coreData['handle'] . '</a>)</div>
					<div class="c-feed-body">' . $coreData['description'] . '</div>';
			
			// Hashtag List
			if($coreData['primary_hashtag'])
			{
				echo '
					<div class="c-tag-wrap">
						<div class="c-tag-prime">
							<div class="c-tp-plus">
								<a class="c-tp-plink" href="' . Feed::follow($coreData['primary_hashtag']) . '"><span class="icon-circle-plus"></span></a>
							</div>
							<a class="c-hlink" href="' . $hashtagURL . '/' . $coreData['primary_hashtag'] . '">#' . $coreData['primary_hashtag'] . '</a>
						</div>
					</div>';
			}
			
			echo '
				</div>
			</div>';
		}
	}
	
}