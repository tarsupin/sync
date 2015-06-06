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

// Retrieve the UniID and Notification Count of the user being called
if(!$userData = User::getDataByHandle($_POST['username'], "uni_id, notify_count"))
{
	User::silentRegister($_POST['username']);
	
	if(!$userData = User::getDataByHandle($_POST['username'], "uni_id, notify_count"))
	{
		exit;
	}
}

// Get the list of notifications for the user
$notifications = AppNotifications::get((int) $userData['uni_id'], 1, max((int) $userData['notify_count'], 5));

// Return the JSON
echo json_encode(array("notification_count" => $userData['notify_count'], "notifications" => $notifications));
