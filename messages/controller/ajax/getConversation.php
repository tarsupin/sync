<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare a response
header('Access-Control-Allow-Origin: *');

// Make sure the appropriate data was sent
if(!isset($_POST['username']) or !isset($_POST['enc']))
{
	exit;
}

// Make sure the encryption passed
if($_POST['enc'] != Security::jsEncrypt($_POST['username']))
{
	exit;
}

// Retrieve the UniID
$uniID1 = User::getIDByHandle($_POST['username']);
$uniID2 = User::getIDByHandle($_POST['otheruser']);

// If a time was set, retrieve messages only since the time provided
if(isset($_POST['time']) and $_POST['time'])
{
	$messages = AppMessages::getMessagesSinceTime($uniID1, $uniID2, (float) $_POST['time']);
}

// If no time was provided, retrieve the last few messages
else
{
	$messages = AppMessages::getMessages($uniID1, $uniID2, 6);
}

// Loop through each message to provide an image to display
foreach($messages as $key => $message)
{
	$messages[$key]['img'] = ProfilePic::image($message['uni_id_' . $message['poster']], "small");
}

// Return the JSON
echo json_encode($messages);
