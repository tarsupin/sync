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
if(!$uniID = User::getIDByHandle($_POST['username']))
{
	// Attempt to silently register the user so that the functions can work appropriately
	if(!User::silentRegister($uniID))
	{
		exit;
	}
}

// Update your online activity
AppFriends::updateActivity($uniID);

// Get your online friend list
$friendList = AppFriends::getActiveFriendList($uniID, 1, 10);

foreach($friendList as $key => $friend)
{
	$friendList[$key]['img'] = ProfilePic::image($friend['uni_id'], "small");
}

// Return the JSON
echo json_encode($friendList);
