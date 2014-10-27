<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare a response
header('Access-Control-Allow-Origin: *');

// Make sure the appropriate data was sent
if(!isset($_POST['username']) or !isset($_POST['enc']) or !isset($_POST['time']))
{
	exit;
}

// Make sure the encryption passed
if($_POST['enc'] != Security::jsEncrypt($_POST['username']))
{
	exit;
}

// Retrieve the UniID for the user calling this request
if(!$myID = User::getIDByHandle($_POST['username']))
{
	if(!$myID = User::silentRegister($_POST['username']))
	{
		exit;
	}
}

// Prepare Values
$_POST['time'] = (float) $_POST['time'];
$currentTime = microtime(true);

// Post a Message
if(isset($_POST['toUser']) and isset($_POST['message']))
{
	// Retrieve the UniID for the person being sent a message
	if(!$recipientID = User::getIDByHandle($_POST['toUser']))
	{
		$recipientID = User::silentRegister($_POST['toUser']);
	}
	
	// If the recipient exists, post a message to them
	if($recipientID and $_POST['message'])
	{
		// Post the message to the user
		AppMessages::sendMessage($myID, $recipientID, Sanitize::text($_POST['message']));
	}
}

// Check the message queue to see who has messaged you since the provided date
if(!$userQueue = AppMessages::checkQueue($myID, $_POST['time']))
{
	echo json_encode(array("time" => $currentTime));
	exit;
}

// Prepare Values
$messages = array();

// If a time was set, retrieve messages only since the time provided
foreach($userQueue as $userData)
{
	$messages[$userData['handle']] = AppMessages::getMessagesSinceTime($myID, (int) $userData['uni_id'], $_POST['time']);
}

// Return the JSON
echo json_encode(array("time" => $currentTime, "messages" => $messages));
