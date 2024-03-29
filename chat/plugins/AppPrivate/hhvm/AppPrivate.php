<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

-----------------------------------------
------ About the AppPrivate Plugin ------
-----------------------------------------

*/

abstract class AppPrivate {
	
	
/****** Retrieve a channel ID by the channel name ******/
	public static function getChannelID
	(
		string $channel		// <str> The channel (hashtag) to get the channel ID of.
	): int					// RETURNS <int> the ID of the resulting channel, or 0 on failure.
	
	// $channelID = AppPrivate::getChannelID($channel);
	{
		return (int) Database::selectValue("SELECT id FROM chat_channels_private WHERE channel=? LIMIT 1", array($channel));
	}
	
	
/****** Retrieve channel data by the channel name ******/
	public static function getChannelData
	(
		string $channel		// <str> The private channel (hashtag) to get the data of.
	): array <str, mixed>					// RETURNS <str:mixed> the data of the resulting channel, or array() on failure.
	
	// $channelData = AppPrivate::getChannelData($channel);
	{
		return Database::selectOne("SELECT id, password FROM chat_channels_private WHERE channel=? LIMIT 1", array($channel));
	}
	
	
/****** Retrieve a channel name by the channel ID ******/
	public static function getChannelNameByID
	(
		int $channelID		// <int> The ID of the channel.
	): string					// RETURNS <str> the name of the channel, or "" on failure.
	
	// $channel = AppPrivate::getChannelNameByID($channelID);
	{
		return Database::selectValue("SELECT channel FROM chat_channels_private WHERE id=? LIMIT 1", array($channelID));
	}
	
	
/****** Retrieve a channel server ID (modular) by the channel name ******/
	public static function getChannelServer
	(
		string $channel		// <str> The channel name.
	): int					// RETURNS <int> the chat's server integer.
	
	// $chatServerID = AppPrivate::getChannelServer($channel);
	{
		return (int) (ord($channel[0]) + ord($channel[1])) % 10;
	}
	
	
/****** Create a new channel ******/
	public static function createChannel
	(
		string $channel		// <str> The channel (hashtag) to create.
	,	string $password		// <str> The password to assign to the channel.
	): int					// RETURNS <int> the ID of the resulting channel, or 0 on failure.
	
	// $channelID = AppPrivate::createChannel($channel, $password);
	{
		// Make sure the password is a valid hash as expected
		$password = Sanitize::safeword($password, "/+=");
		
		// Check to see if the channel already exists
		if($channelID = self::getChannelID($channel))
		{
			return $channelID;
		}
		
		// Get the Server ID that this channel is meant for
		$loadServerID = self::getChannelServer($channel);
		
		// Get the Chat Server ID
		$host = explode(".", $_SERVER['SERVER_NAME']);
		$chatServID = (int) str_replace("pchat", "", $host[0]);
		
		// If you're not on the correct server
		if($loadServerID != $chatServID)
		{
			return 0;
		}
		
		// Create the new channel
		Database::query("INSERT INTO chat_channels_private (channel, password) VALUES (?, ?)", array($channel, $password));
		
		return Database::$lastID;
	}
	
	
/****** Update an existing channel ******/
	public static function updateChannel
	(
		string $channel		// <str> The channel (hashtag) to update.
	,	string $password		// <str> The password to update.
	,	string $oldPassword	// <str> The existing password for the channel.
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppPrivate::updateChannel($channel, $password, $oldPassword);
	{
		// Get the Server ID that this channel is meant for
		$loadServerID = self::getChannelServer($channel);
		
		// Get the Chat Server ID
		$host = explode(".", $_SERVER['SERVER_NAME']);
		$chatServID = (int) str_replace("pchat", "", $host[0]);
		
		// If you're not on the correct server
		if($loadServerID != $chatServID)
		{
			return false;
		}
		
		// Check if your current password is correct
		if(Database::selectValue("SELECT password FROM chat_channels_private WHERE channel=? AND password=? LIMIT 1", array($channel, $oldPassword)))
		{
			// Update the new channel
			return Database::query("REPLACE INTO chat_channels_private (channel, password) VALUES (?, ?)", array($channel, $password));
		}
		
		return false;
	}
	
	
/****** Post a message to a chat ******/
	public static function postMessage
	(
		int $channelID		// <int> The channel ID to post a message to.
	,	int $uniID			// <int> The UniID posting a message.
	,	string $message		// <str> The message being posted to the chat.
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppPrivate::postMessage($channelID, $uniID, $message);
	{
		return Database::query("REPLACE INTO chat_messages_private (channel_id, date_posted, uni_id, message) VALUES (?, ?, ?, ?)", array($channelID, microtime(true), $uniID, $message));
	}
	
	
/****** Retrieve the most recent list of chat messages ******/
	public static function getMessages
	(
		int $channelID			// <int> The channel ID to retrieve messages from.
	,	int $messageCount = 30	// <int> The number of messages to retrieve.
	): array <int, array<str, mixed>>						// RETURNS <int:[str:mixed]> a list of messages with appropriate data included.
	
	// $messages = AppPrivate::getMessages($channelID, [$messageCount]);
	{
		$messages = Database::selectMultiple("SELECT c.uni_id, u.handle, c.message, c.date_posted FROM chat_messages_private c INNER JOIN users u ON c.uni_id=u.uni_id WHERE c.channel_id=? ORDER BY c.date_posted DESC LIMIT " . ($messageCount + 0), array($channelID));
		
		return array_reverse($messages);
	}
	
	
/****** Retrieve chat messages that were posted since a designated time ******/
	public static function getMessagesSinceTime
	(
		int $channelID		// <int> The channel ID to retrieve messages from.
	,	float $microtime		// <float> The microtime of the post to use the message retrieval.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> a list of messages with appropriate data included.
	
	// $messages = AppPrivate::getMessagesSinceTime($channelID, $microtime);
	{
		$messages = Database::selectMultiple("SELECT c.uni_id, u.handle, c.message, c.date_posted FROM chat_messages_private c INNER JOIN users u ON c.uni_id=u.uni_id WHERE c.channel_id=? AND c.date_posted > ? ORDER BY c.date_posted DESC LIMIT 30", array($channelID, $microtime));
		
		return array_reverse($messages);
	}
	
	
/****** Prune a channel of old data ******/
	public static function pruneChannel
	(
		int $channelID			// <int> The channel ID to retrieve messages from.
	,	int $pruneCount = 250	// <int> The number of messages to leave prior to pruning.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppPrivate::pruneChannel($channelID, [$pruneCount]);
	{
		$count = Database::query("SELECT COUNT(*) as totalNum FROM chat_messages_private WHERE channel_id=? LIMIT 1", array($channelID));
		
		if($count >= $pruneCount + 50)
		{
			$numToRemove = $count - $pruneCount;
			
			return Database::query("DELETE FROM chat_messages_private WHERE channel_id=? ORDER BY date_posted LIMIT " . ($numToRemove + 0), array($channelID));
		}
		
		return true;
	}
	
}