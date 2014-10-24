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

// Retrieve the UniID of the user being called
$uniID = User::getIDByHandle($_POST['username']);

// Get the list of notifications for the user
$notifications = AppNotifications::resetCount($uniID);

// Return the JSON
echo true;