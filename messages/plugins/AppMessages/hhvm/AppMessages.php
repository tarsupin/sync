<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

-----------------------------------------
------ About the AppMessages Plugin ------
-----------------------------------------

This plugin provides a set of tools to send messages between two users.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppMessages {
	
	
/****** Get a list of messages between two users ******/
	public static function getMessages
	(
		int $uniID1				// <int> The first UniID involved in the exchange.
	,	int $uniID2				// <int> The second UniID involved in the exchange.
	,	int $messageCount = 15	// <int> The number of messages to return.
	): array <int, array<str, mixed>>						// RETURNS <int:[str:mixed]> a list of messages to return.
	
	// $messages = AppMessages::getMessages($uniID1, $uniID2, [$messageCount]);
	{
		// Make sure they're not sending to themselves
		if($uniID1 == $uniID2) { return false; }
		
		// Make sure that UniID1 is positioned first (lower sequentially)
		if($uniID1 > $uniID2)
		{
			$tmp = $uniID2;
			$uniID2 = $uniID1;
			$uniID1 = $tmp;
		}
		
		// Retrieve the list of messages between the two users
		$results = Database::selectMultiple("SELECT uni_id_1, uni_id_2, date_posted, poster, message FROM user_messages WHERE uni_id_1=? AND uni_id_2=? ORDER BY date_posted DESC LIMIT " . ($messageCount + 0), array($uniID1, $uniID2));
		
		return array_reverse($results);
	}
	
	
/****** Get a list of messages between two users ******/
	public static function getMessagesSinceTime
	(
		int $uniID1			// <int> The first UniID involved in the exchange.
	,	int $uniID2			// <int> The second UniID involved in the exchange.
	,	float $microtime		// <float> The timestamp to return messages after.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> A list of messages to return.
	
	// $messages = AppMessages::getMessagesSinceTime($uniID1, $uniID2, [$microtime]);
	{
		// Make sure they're not sending to themselves
		if($uniID1 == $uniID2) { return false; }
		
		// Make sure that UniID1 is positioned first (lower sequentially)
		if($uniID1 > $uniID2)
		{
			$tmp = $uniID2;
			$uniID2 = $uniID1;
			$uniID1 = $tmp;
		}
		
		// Retrieve the list of messages between the two users
		$results = Database::selectMultiple("SELECT uni_id_1, uni_id_2, date_posted, poster, message FROM user_messages WHERE uni_id_1=? AND uni_id_2=? AND date_posted > ? ORDER BY date_posted DESC LIMIT 10", array($uniID1, $uniID2, $microtime));
		
		$chatData = array();
		
		// Loop through the results to add the img value
		foreach($results as $key => $res)
		{
			$chatData[] = array(
				'img'		=> ProfilePic::image((int) $res['uni_id_' . $res['poster']], "small")
			,	'message'	=> $res['message']
			);
		}
		
		return array_reverse($chatData);
	}
	
	
/****** Create a message between two users ******/
	public static function sendMessage
	(
		int $senderID		// <int> The UniID that is sending the message.
	,	int $recipientID	// <int> The UniID that is receiving the message.
	,	string $message		// <str> The message being sent.
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppMessages::sendMessage($senderID, $recipientID, $message);
	{
		// Make sure they're not sending to themselves
		if($senderID == $recipientID) { return false; }
		
		// Determine which UniID is sequentially first
		if($senderID > $recipientID)
		{
			$uniID1 = $recipientID;
			$uniID2 = $senderID;
			$poster = 2;
		}
		else
		{
			$uniID1 = $senderID;
			$uniID2 = $recipientID;
			$poster = 1;
		}
		
		// Update the recipient's message queue
		Database::query("REPLACE INTO user_messages_queue (uni_id_1, uni_id_2, date_updated) VALUES (?, ?, ?)", array($recipientID, $senderID, microtime(true)));
		
		// Occasionally prune old data
		if(mt_rand(0, 50) == 22)
		{
			// Prune the inbox messages
			self::pruneInbox($uniID1, $uniID2);
		}
		
		// Add the message
		return Database::query("INSERT INTO user_messages (uni_id_1, uni_id_2, date_posted, poster, message) VALUES (?, ?, ?, ?, ?)", array($uniID1, $uniID2, microtime(true), $poster, $message));
	}
	
	
/****** Check the queue to receive a list of users who have updated since a given time ******/
	public static function checkQueue
	(
		int $uniID			// <int> The UniID that is having their message queue checked.
	,	float $microtime		// <float> The timestamp that indicates when to ignore any further queued information.
	): array <int, str>					// RETURNS <int:str> a list of users that have updated since the time designated.
	
	// $users = AppMessages::checkQueue($uniID, $microtime);
	{
		return Database::selectMultiple("SELECT u.uni_id, u.handle FROM user_messages_queue q INNER JOIN users u ON q.uni_id_2=u.uni_id WHERE q.uni_id_1=? AND q.date_updated > ?", array($uniID, $microtime));
	}
	
	
/****** Create a message between two users ******/
	public static function pruneInbox
	(
		int $uniID1		// <int> The UniID that is sending the message.
	,	int $uniID2		// <int> The UniID that is receiving the message.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppMessages::pruneInbox($uniID1, $uniID2);
	{
		// Transfer old contents of user_messages into user_messages_old
	}
	
}