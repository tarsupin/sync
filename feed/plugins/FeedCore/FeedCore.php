<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the FeedCore Plugin ------
---------------------------------------

This plugin provides all of the necessary functionality to run the core feed system.

-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class FeedCore {
	
	
/****** Plugin Variables ******/
	public static $pruneDuration = 2592000;		// <int> Duration of time before pruning; default is one month.
	public static $feedID = 0;					// <int> The Feed ID of a newly created entry.
	
	
/****** Get a Feed Entry ******/
	public static function get
	(
		$feedID		// <int> The ID of the feed entry to retrieve.
	)				// RETURNS <str:mixed> the data of the feed entry.
	
	// $feedData = FeedCore::get($feedID);
	{
		return Database::selectOne("SELECT d.*, u.handle FROM feed_data d LEFT JOIN users u ON d.author_id=u.uni_id WHERE d.id=? LIMIT 1", array($feedID));
	}
	
	
/****** Create or update a Feed Entry ******/
	public static function setEntry
	(
		$siteHandle		// <str> The site handle that submitted the entry.
	,	$authorID		// <int> The UniID that authored the original content.
	,	$url			// <str> The URL that the entry will be sourced from.
	,	$title			// <str> The title of the entry.
	,	$description	// <str> The description for the entry.
	,	$thumbnail		// <str> The URL of the image to associate with the entry, if applicable.
	,	$primeHashtag	// <str> The primary hashtag associated with the feed.
	,	$hashtagList	// <int:str> The list of hashtags associated with the feed.
	)					// RETURNS <bool> TRUE if the entry is created, FALSE if not.
	
	// FeedCore::setEntry($siteHandle, $authorID, $url, $title, $description, $thumbnail, $primeHashtag, $hashtagList);
	{
		// Add the primary hashtag to the hashtag list, if applicable
		if(!in_array($primeHashtag, $hashtagList))
		{
			$hashtagList[] = $primeHashtag;
		}
		
		// Prepare Values
		$hashtagStr = "";
		
		foreach($hashtagList as $ht)
		{
			$hashtagStr .= ($hashtagStr ? " " : "") . $ht;
		}
		
		// Begin the database transaction
		Database::startTransaction();
		
		if($pass = Database::query("INSERT INTO feed_data (site_handle, primary_hashtag, hashtags, author_id, url, title, description, thumbnail, date_posted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($siteHandle, $primeHashtag, $hashtagStr, $authorID, $url, $title, $description, $thumbnail, time())))
		{
			if(!self::$feedID = Database::$lastID)
			{
				$pass = false;
			}
			
			// Loop through each of the hashtags and submit it to the system
			foreach($hashtagList as $ht)
			{
				if(!$pass = Database::query("INSERT INTO feed_posts (hashtag, feed_id) VALUES (?, ?)", array($ht, self::$feedID)))
				{
					break;
				}
			}
		}
		
		return Database::endTransaction($pass);
	}
	
	
/****** Prune older Feed Entries ******/
	public static function pruneEntries (
	)					// RETURNS <bool> TRUE if the entry is created, FALSE if not.
	
	// FeedCore::pruneEntries();
	{
		// Get the first ID created (so that we're only running this algorithm on the oldest values)
		if(!$checkID = (int) Database::selectValue("SELECT id FROM feed_data ORDER BY id DESC LIMIT 1", array()))
		{
			return false;
		}
		
		// Prepare Values
		$sourceTable = "feed_data";
		$destinationTable = "feed_data_old";
		$sqlWhere = "id < ? AND date_posted < ?";
		$sqlInput = array($checkID + 1000, time() - self::$pruneDuration);
		$sqlLimit = 1000;
		
		// Run the Pruning Action
		return DBTransfer::move($sourceTable, $destinationTable, $sqlWhere, $sqlInput, $sqlLimit);
	}
	
}
