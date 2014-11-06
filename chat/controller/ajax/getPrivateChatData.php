<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare a response
header('Access-Control-Allow-Origin: *');

// Make sure that the necessary information is posted
if(!isset($_POST['channel']) or !isset($_POST['username']) or !isset($_POST['enc']))
{
	exit;
}

// End this API if the private channel does not exist
if(!$channelData = AppPrivate::getChannelData($_POST['channel']))
{
	exit;
}

// Prepare Values
$channelID = (int) $channelData['id'];
$_POST['lastpost'] = (float) $_POST['lastpost'];

// Make sure the encryption passed
if($_POST['enc'] != Security::jsEncrypt($_POST['username'], $channelData['password']))
{
	exit;
}

// If the user submitted a message, run the message post
if(isset($_POST['message']) and $_POST['message'])
{
	// Prepare Values
	$_POST['message'] = Sanitize::text($_POST['message']);
	
	if(!$uniID = User::getIDByHandle($_POST['username']))
	{
		User::silentRegister($_POST['username']);
		
		$uniID = User::getIDByHandle($_POST['username']);
	}
	
	// If the user exists, let them post
	if($uniID)
	{
		// If the channel doesn't exist yet, create it
		if(!$channelID)
		{
			// If the channel creation fails, end the function
			if(!$channelID = AppPrivate::createChannel($_POST['channel']))
			{
				exit;
			}
		}
		
		AppPrivate::postMessage($channelID, $uniID, $_POST['message']);
	}
}

// Retrieve chat messages since the last time designated
if(!$messages = AppPrivate::getMessagesSinceTime($channelID, $_POST['lastpost']))
{
	exit;
}

$lastTime = (float) $messages[count($messages) - 1]["date_posted"];

$response = array();

foreach($messages as $message)
{
	$response[] = array("img" => ProfilePic::image($message['uni_id'], "small"), "time" => date("G:i", $message['date_posted']), "message" => $message['message']);
}

echo json_encode(array("last_time" => $lastTime, "messages" => $response));
