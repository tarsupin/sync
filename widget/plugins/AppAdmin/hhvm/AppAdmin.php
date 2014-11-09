<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the AppAdmin Plugin ------
---------------------------------------

This plugin provides a set of methods to administer important functionality on the featured widget.


*/

abstract class AppAdmin {
	
	
/****** Create a parent for a specific hashtag ******/
	public static function createParent
	(
		string $hashtag			// <str> The hashtag to assign a parent to.
	,	string $parentHashtag		// <str> The parent hashtag to assign to the hashtag.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAdmin::createParent($hashtag, $parentHashtag);
	{
		return Database::query("REPLACE INTO widget_parents (hashtag, parent) VALUES (?, ?)", array($hashtag, $parentHashtag));
	}
	
	
/****** Assign a featured entry to a designated hashtag by the title ******/
	public static function assignEntryByTitle
	(
		string $entryTitle			// <str> The title of the featured entry to assign.
	,	string $hashtag			// <str> The hashtag to assign the featured entry to.
	,	string $category			// <str> The category to assign the featured entry to.
	,	string $verb				// <str> The verb to assign the featured entry to.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAdmin::assignEntryByTitle($entryTitle, $hashtag, $category, $verb);
	{
		// Get the ID of the entry based on the title
		if($entryID = (int) Database::selectValue("SELECT id FROM widget_featured WHERE title=? LIMIT 1", array($entryTitle)))
		{
			return AppFeatured::assign($entryID, $hashtag, $category, $verb);
		}
		
		return false;
	}
}