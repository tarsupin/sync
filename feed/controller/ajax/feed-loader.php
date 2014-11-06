<?php

// Make sure the appropriate data is sent
if(!isset($_GET['startPos']) or !Me::$loggedIn)
{
	exit;
}

// Get the User's Feed Posts
if(!$contentIDs = UserFeed::getFeedIDs(Me::$id, (int) $_GET['startPos']))
{
	// If there are no $contentIDs, exit here to prevent UserFeed::displayFeed() from writing "No articles available"
	exit;
}

// Load the next set of user feed posts
UserFeed::displayFeed($contentIDs);