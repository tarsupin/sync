<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Force the user to log in on this site
if(!Me::$loggedIn)
{
	Me::redirectLogin("/", "/welcome");
}

// Update the active user's feed (if they're not logged in, it skips them)
UserFeed::updateFeed();

// Prepare the User Feed
UserFeed::prepare();

// Get the User's Feed Posts
$contentIDs = UserFeed::getFeedIDs(Me::$id);

// Run an action
if(isset($_GET['action']))
{
	switch($_GET['action'])
	{
		// Run Tip Exchanges
		case "tip":
			if($getData = Link::getData("send-tip-feed") and is_array($getData) and isset($getData[0]))
			{
				// Get the user from the post
				Credits::tip(Me::$id, (int) $getData[0]);
			}
			break;
		
		// "Chat This" action
		case "chat":
			if($getData = Link::getData("chat-feed") and is_array($getData) and isset($getData[0]))
			{
				// Gather details about this content
				if($feedData = FeedCore::get($getData[0]))
				{
					Share::chatArticle(Me::$id, $feedData['image_url'], "", $feedData['blurb'], $feedData['title'], $feedData['url'], $feedData['handle'], "article");
				}
			}
			break;
		
		// "Share This" action
		case "share":
			if($getData = Link::getData("share-feed") and is_array($getData) and isset($getData[0]))
			{
				// Gather details about this content
				if($feedData = FeedCore::get($getData[0]))
				{
					Share::socialArticle(Me::$id, $feedData['title'], $feedData['blurb'], $feedData['url'], $feedData['image_url'], "", "article");
				}
			}
			break;
	}
}

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Run the auto-scrolling script
echo '
<script>
	urlToLoad = "/ajax/feed-loader";
	elementIDToAutoScroll = "personal-feed";
	startPos = 2;
	entriesToReturn = 1;
	maxEntriesAllowed = 20;
	waitDuration = 1200;
	appendURL = "";		// "&example=1"
	
	function afterAutoScroll()
	{
		// Nothing to run
	}
</script>';

// Load the Page
echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

if(count($contentIDs) == 0)
{
	echo '
	<div style="padding:12px;">
		Your feed is currently empty.<br /><br />
		
		If you would like to see content populate your feed, click the "Follow" button available on the articles you find interesting. You will begin receiving updates of similar articles being posted.
	</div>';
}
else
{
	echo '
	<div id="personal-feed">';
	
	UserFeed::displayFeed($contentIDs);
	
	echo '
	</div>';
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");