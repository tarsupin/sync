<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

-----------------------------------------------
------ About the AppNotifications Plugin ------
-----------------------------------------------

This plugin provides methods to interact with notifications.


-------------------------------
------ Methods Available ------
-------------------------------

AppNotifications::add($uniID, $url, $message);

*/

abstract class AppNotifications {
	
	
/****** Get a list of a user's notifications ******/
	public static function get
	(
		$uniID				// <int> The UniID to get notifications from.
	,	$page = 1			// <int> The page to start at (pagination value).
	,	$returnNum = 5		// <int> The total number of rows to return.
	)						// RETURNS <int:[str:mixed]> the list of notifications for the user.
	
	// $notifications = AppNotifications::get($uniID, [$page], [$returnNum]);
	{
		return Database::selectMultiple("SELECT url, message, date_created FROM notifications WHERE uni_id=? ORDER BY date_created DESC LIMIT " . (($page -1) * $returnNum) . ", " . ($returnNum + 0), array($uniID));
	}
	
	
/****** Add a notification ******/
	public static function add
	(
		$uniID			// <int> The UniID to add a notification to.
	,	$url			// <str> The URL that is assigned to the notification.
	,	$message		// <str> The message of the notification.
	)					// RETURNS <bool> TRUE if successfully added, FALSE on failure.
	
	// AppNotifications::add($uniID, $url, $message);
	{
		// Purge every few times this method is called
		if(mt_rand(0, 20) == 5)
		{
			self::purge($uniID);
		}
		
		// Update the user's notification count
		self::incrementCount($uniID);
		
		// Insert a notification
		return Database::query("INSERT INTO notifications (uni_id, url, message, date_created) VALUES (?, ?, ?, ?)", array($uniID, $url, $message, time()));
	}
	
	
/****** Purge a user's notifications ******/
	public static function purge
	(
		$uniID			// <int> The UniID to purge old notifications for.
	)					// RETURNS <bool> TRUE if successfully added, FALSE on failure.
	
	// AppNotifications::purge($uniID);
	{
		// Check how many rows the table has
		$checkCount = (int) Database::selectValue("SELECT COUNT(*) FROM notifications WHERE uni_id=? LIMIT 1", array($uniID));
		
		if($checkCount > 120)
		{
			$remove = $checkCount - 100;
			
			return Database::query("DELETE FROM notifications WHERE uni_id=? ORDER BY date_created ASC LIMIT " . ($remove + 0), array($uniID));
		}
		
		return false;
	}
	
	
/****** Increment the number of user's notifications by 1 ******/
	public static function incrementCount
	(
		$uniID			// <int> The UniID to increment the notifications for.
	)					// RETURNS <bool> TRUE if successfully added, FALSE on failure.
	
	// AppNotifications::incrementCount($uniID);
	{
		// Get the user data necessary
		if(!$userData = Database::selectOne("SELECT uni_id, notify_count FROM users WHERE uni_id=? LIMIT 1", array($uniID)))
		{
			// Silently register the user
			if(!$userData = User::silentRegister($uniID))
			{
				return false;
			}
		}
		
		// Update the notification count
		return Database::query("UPDATE users SET notify_count=notify_count+1 WHERE uni_id=? LIMIT 1", array($uniID));
	}
	
	
/****** Reset the user's notifications to 0 ******/
	public static function resetCount
	(
		$uniID			// <int> The UniID to increment the notifications for.
	)					// RETURNS <bool> TRUE if successfully added, FALSE on failure.
	
	// AppNotifications::resetCount($uniID);
	{
		return Database::query("UPDATE IGNORE users SET notify_count=? WHERE uni_id=? LIMIT 1", array(0, $uniID));
	}
	
}
