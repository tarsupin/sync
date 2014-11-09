<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

------------------------------------------
------ About the AppFeatured Plugin ------
------------------------------------------

This plugin provides a set of methods to update the featured widget.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppFeatured {
	
	
/****** Create the featured entry ******/
	public static function createEntry
	(
		$title				// <str> The title of the featured entry.
	,	$description		// <str> The description of the featured entry.
	,	$url				// <str> The URL that the entry will point to.
	)						// RETURNS <int> The ID of the entry created, or 0 on failure.
	
	// $entryID = AppFeatured::createEntry($title, $description, $url);
	{
		if(Database::query("INSERT INTO widget_featured (title, description, url, date_created) VALUES (?, ?, ?, ?)", array($title, $description, $url, time())))
		{
			return Database::$lastID;
		}
		
		return 0;
	}
	
	
/****** Get the featured entry ******/
	public static function getEntryByID
	(
		$entryID	// <int> The ID of the entry to retrieve.
	)				// RETURNS <str:mixed> The content of the entry, or array() on failure.
	
	// $entryData = AppFeatured::getEntryByID($entryID);
	{
		return Database::selectOne("SELECT * FROM widget_featured WHERE id=? LIMIT 1", array($entryID));
	}
	
	
/****** Get the parent hashtags of a designated hashtag ******/
	public static function getParents
	(
		$hashtag	// <str> The hashtag to get the parent of.
	)				// RETURNS <int:str> the parent hashtag, or array() if there are no parents.
	
	// $parentHashtags = AppFeatured::getParents($hashtag);
	{
		$parentList = array();
		
		$results = Database::selectMultiple("SELECT parent FROM widget_parents WHERE hashtag=?", array($hashtag));
		
		foreach($results as $res)
		{
			$parentList[] = $res['parent'];
		}
		
		return $parentList;
	}
	
	
/****** Assign a featured entry to a Hashtag + Category combo ******/
	public static function assign
	(
		$entryID		// <int> The ID of the featured entry to assign.
	,	$hashtag		// <str> The hashtag to assign the entry to.
	,	$category		// <str> The category to assign the entry to.
	,	$verb			// <str> The verb to assign the entry to.
	)					// RETURNS <bool> TRUE on successfully created, FALSE on failure.
	
	// AppFeatured::assign($entryID, $hashtag, $category, $verb);
	{
		return Database::query("REPLACE INTO widget_featured_pull (hashtag, category, verb, entry_id) VALUES (?, ?, ?, ?)", array($hashtag, $category, $verb, $entryID));
	}
	
	
/****** Get a random verb from a hashtag / category combo ******/
	public static function getRandomVerb
	(
		$hashtag		// <str> The hashtag to pull from.
	,	$category		// <str> The category to pull from.
	)					// RETURNS <str> the random verb that was recovered.
	
	// $verb = AppFeatured::getRandomVerb($hashtag, $category);
	{
		// Prepare Values
		$sqlWhere = "";
		$sqlArray = array();
		
		// Get the hashtag's parent
		if($parents = AppFeatured::getParents($hashtag))
		{
			$parents[] = $hashtag;
			
			list($sqlWhere, $sqlArray) = Database::sqlFilters(array("hashtag" => $parents, "category" => array($category)));
		}
		else
		{
			$sqlWhere = "hashtag=? AND category=?";
			$sqlArray = array($hashtag, $category);
		}
		
		// Run the query
		return (string) Database::selectValue("SELECT DISTINCT verb FROM widget_featured_pull WHERE " . $sqlWhere . " ORDER BY RAND() LIMIT 1", $sqlArray);
	}
	
	
/****** Get the full list of featured entries by hashtag and category combos ******/
	public static function getAssigned
	(
		$hashtag		// <str> The hashtag to pull from.
	,	$category = ""	// <str> The category to pull from.
	,	$verb = ""		// <str> The verb to pull from.
	)					// RETURNS <int:[str:mixed]> an array of results for the designated hashtag and category.
	
	// $widgetData = AppFeatured::getAssigned($hashtag, [$category], [$verb]);
	{
		// Prepare Values
		$sqlWhere = "p.hashtag=?";
		$sqlArray = array($hashtag);
		
		if($category)
		{
			$sqlWhere .= " AND p.category=?";
			$sqlArray[] = $category;
			
			if($verb)
			{
				$sqlWhere .= " AND p.verb=?";
				$sqlArray[] = $verb;
			}
		}
		
		return Database::selectMultiple("SELECT p.* FROM widget_featured_pull p INNER JOIN widget_featured f ON p.entry_id=f.id WHERE " . $sqlWhere, $sqlArray);
	}
	
	
/****** Pull a random set of featured entries by hashtag and category combos ******/
	public static function pull
	(
		$hashtag		// <str> The hashtag to pull from.
	,	$category = ""	// <str> The category to pull from.
	,	$verb = ""		// <str> The verb to pull from.
	,	$numReturn = 3	// <int> The number of slots to return
	)					// RETURNS <int:[str:mixed]> an array of results for the designated hashtag and category.
	
	// $widgetData = AppFeatured::pull($hashtag, [$category], [$verb], [$numReturn]);
	{
		// Prepare Values
		$sqlWhere = "p.hashtag=?";
		$sqlArray = array($hashtag);
		
		// Get the hashtag's parent
		if($parents = AppFeatured::getParents($hashtag))
		{
			$parents[] = $hashtag;
			
			list($sqlWhere, $sqlArray) = Database::sqlFilters(array("hashtag" => $parents));
		}
		
		// If specifying a category and/or verb, add the appropriate search parameters
		if($category)
		{
			$sqlWhere .= " AND p.category=?";
			$sqlArray[] = $category;
			
			if($verb)
			{
				$sqlWhere .= " AND p.verb=?";
				$sqlArray[] = $verb;
			}
		}
		
		// Run the query
		return Database::selectMultiple("SELECT f.* FROM widget_featured_pull p INNER JOIN widget_featured f ON p.entry_id=f.id WHERE " . $sqlWhere . " ORDER BY RAND() LIMIT " . ($numReturn + 0), $sqlArray);
	}
	
	
/****** Update the number of views for a featured widget ******/
	public static function updateViews
	(
		$widgetData		// <int:[str:mixed]> The widget data to update views for.
	,	$viewCount		// <int> The number of views to add to each entry being pulled.
	)					// RETURNS <void>
	
	// AppFeatured::updateViews($widgetData, $viewCount);
	{
		foreach($widgetData as $wData)
		{
			Database::query("UPDATE widget_featured SET views=views+? WHERE id=? LIMIT 1", array($viewCount, $wData['id']));
		}
	}
}
