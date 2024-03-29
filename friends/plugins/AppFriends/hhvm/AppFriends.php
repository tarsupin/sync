<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

-----------------------------------------
------ About the AppFriends Plugin ------
-----------------------------------------

This plugin provides methods to interact with friends.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppFriends {
	
	
/****** Update a user's activity to the current time ******/
	public static function updateActivity
	(
		int $uniID				// <int> The UniID to update the online activity of.
	): void						// RETURNS <void>
	
	// AppFriends::updateActivity($uniID);
	{
		// Check your last activity
		$lastActivity = (int) Database::selectValue("SELECT last_activity FROM users_activity WHERE uni_id=? LIMIT 1", array($uniID));
		
		// Update your online friends list occasionally
		if($lastActivity < time() - 25)
		{
			self::updateActiveFriendList($uniID);
			
			Database::query("REPLACE INTO users_activity (uni_id, last_activity) VALUES (?, ?)", array($uniID, time()));
		}
	}
	
	
/****** Check if two users are friends with each other ******/
	public static function isFriend
	(
		int $uniID			// <int> The UniID of the first user to verify is a friend.
	,	int $friendID		// <int> The UniID of the friend to verify.
	): bool					// RETURNS <bool> TRUE if they are friends, FALSE if not.
	
	// AppFriends::isFriend($uniID, $friendID);
	{
		// Every once in a while, run a full activity purge
		if(mt_rand(0, 2000) == 22)
		{
			self::fullActivityPurge();
		}
		
		// Check if the users are friends
		if($check = Database::selectValue("SELECT friend_id FROM friends_list WHERE uni_id=? AND friend_id=? LIMIT 1", array($uniID, $friendID)))
		{
			return true;
		}
		
		return false;
	}
	
	
/****** Get a list of a user's online friends, in order of relevance ******/
	public static function getActiveFriendList
	(
		int $uniID			// <int> The UniID to get a list of friends for.
	,	int $page = 1		// <int> The page number of friends to return.
	,	int $returnNum = 20	// <int> The number of active friends to return per page.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> the list of friends for the user.
	
	// $friendList = AppFriends::getActiveFriendList($uniID, [$page], [$returnNum]);
	{
		return Database::selectMultiple("SELECT u.uni_id, u.handle, u.display_name FROM friends_active a INNER JOIN users u ON a.friend_id=u.uni_id WHERE a.uni_id=? ORDER BY u.handle", array($uniID));
	}
	
	
/****** Get a list of a user's full friends, in order of relevance ******/
	public static function getFullFriendList
	(
		int $uniID			// <int> The UniID to get a list of friends for.
	,	int $page = 1		// <int> The page number of friends to return.
	,	int $returnNum = 50	// <int> The number of active friends to return per page.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> the list of friends for the user.
	
	// $friendList = AppFriends::getFullFriendList($uniID, [$page], [$returnNum]);
	{
		return Database::selectMultiple("SELECT u.uni_id, u.handle, u.display_name FROM friends_list f INNER JOIN users u ON f.friend_id=u.uni_id WHERE f.uni_id=?", array($uniID));
	}
	
	
/****** Update the active friend list ******/
	public static function updateActiveFriendList
	(
		int $uniID			// <int> The UniID to update the active friend list for.
	): void					// RETURNS <void>
	
	// AppFriends::updateActiveFriendList($uniID);
	{
		// Set the duration of how recent the user must be to appear online
		$timestamp = time() - 90;
		
		// Purge the user's current active friend list
		Database::query("DELETE FROM friends_active WHERE uni_id=?", array($uniID));
		
		// Gather a list of all of the user's online friends
		if(!$result = Database::selectMultiple("SELECT f.friend_id FROM friends_list f INNER JOIN users_activity a ON f.friend_id=a.uni_id WHERE f.uni_id=? AND a.last_activity >= ?", array($uniID, $timestamp)))
		{
			// If there are no friends online, end here
			return;
		}
		
		// Prepare Values
		$sqlWhere = "";
		$sqlArray = array();
		
		foreach($result as $res)
		{
			$sqlWhere .= ($sqlWhere == "" ? "" : ", ") . "(?, ?)";
			
			$sqlArray[] = $uniID;
			$sqlArray[] = (int) $res['friend_id'];
		}
		
		// Add the online friends into the user's active friend list
		Database::query("REPLACE INTO friends_active (uni_id, friend_id) VALUES " . $sqlWhere, $sqlArray);
	}
	
	
/****** Add a friend ******/
	public static function add
	(
		int $uniID			// <int> The UniID of the first user.
	,	int $friendID		// <int> The UniID of the friend.
	): bool					// RETURNS <bool> TRUE if the friend is successfully added, FALSE on failure.
	
	// AppFriends::add($uniID, $friendID);
	{
		// Verify Users
		if(!self::verifyUsersRegistered($uniID, $friendID))
		{
			return false;
		}
		
		Database::startTransaction();
		
		// Add the friends into the friend list
		if($pass = Database::query("REPLACE INTO friends_list (uni_id, friend_id) VALUES (?, ?)", array($uniID, $friendID)))
		{
			$pass = Database::query("REPLACE INTO friends_list (uni_id, friend_id) VALUES (?, ?)", array($friendID, $uniID));
		}
		
		return Database::endTransaction($pass);
	}
	
	
/****** Remove a friend ******/
	public static function remove
	(
		int $uniID			// <int> The UniID of the first user.
	,	int $friendID		// <int> The UniID of the friend.
	): bool					// RETURNS <bool> TRUE if the friend is successfully removed, FALSE on failure.
	
	// AppFriends::remove($uniID, $friendID);
	{
		Database::startTransaction();
		
		if($pass = Database::query("DELETE FROM friends_list WHERE uni_id=? AND friend_id=? LIMIT 1", array($uniID, $friendID)))
		{
			$pass = Database::query("DELETE FROM friends_list WHERE uni_id=? AND friend_id=? LIMIT 1", array($friendID, $uniID));
		}
		
		return Database::endTransaction($pass);
	}
	
	
/****** Verify that the supplied users are registered to this site ******/
	public static function verifyUsersRegistered (
	): bool					// RETURNS <bool> TRUE if all users are verified, FALSE if not.
	
	// AppFriends::verifyUsersRegistered($uniID, [...$uniID], [...$uniID]);
	{
		$users = func_get_args();
		$pass = true;
		
		foreach($users as $uniID)
		{
			// Check if the user is registered
			if(!$check = Database::selectValue("SELECT uni_id FROM users WHERE uni_id=? LIMIT 1", array((int) $uniID)))
			{
				if(!$check = User::silentRegister((int) $uniID))
				{
					$pass = false;
					break;
				}
			}
		}
		
		return $pass;
	}
	
	
/****** Run a full activity purge - any offline users have their activity data cleansed ******/
	public static function fullActivityPurge (
	): void					// RETURNS <void>
	
	// AppFriends::fullActivityPurge();
	{
		// Declare a time of over 2 hours
		$timestamp = time() - 7200;
		
		// Delete users activity lists that haven't been active for a while
		Database::query("DELETE FROM friends_active WHERE uni_id IN (SELECT uni_id FROM users_activity WHERE last_activity <= ? LIMIT 2000)", array($timestamp));
	}
}