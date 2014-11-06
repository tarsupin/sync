<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure the proper information is passed to this page
if(!isset($channelID))
{
	header("Location: /"); exit;
}

// Prepare Values
$_POST['chat_message'] = isset($_POST['chat_message']) ? Sanitize::text($_POST['chat_message']) : "";

// Run the Chat Message Form
if(Form::submitted("chat-post"))
{
	// Make sure the message isn't too long
	if(strlen($_POST['chat_message']) >= 250)
	{
		Alert::error("Chat Length", "The chat message is too long.");
	}
	
	if(FormValidate::pass())
	{
		// Post the Message
		AppChat::postMessage($channelID, Me::$id, $_POST['chat_message']);
		
		// Clear the Values
		$_POST['chat_message'] = "";
	}
}

// Get the list of messages for this channel
$messages = AppChat::getMessages($channelID, 20);

// Add a JS script to the footer
Metadata::addFooter('<script src="/assets/scripts/chat.js"></script>');

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display the page
echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

echo '
<h3>#UniFaction Chat</h3>
<div id="chat_box">';

foreach($messages as $message)
{
	echo '
	<div class="chat_line">
		<div class="chat_left"><img src="' . ProfilePic::image($message['uni_id'], "small") . '" /></div>
		<div class="chat_right">[' . date("G:i", $message['date_posted']) . '] ' . $message['message'] . '</div>
	</div>';
}

echo '
</div>';

echo '
<div style="margin-top:14px;">';

if(Me::$loggedIn)
{
	echo '
	<form id="chat-form" class="uniform" action="/' . $url[0] . '" method="post">' . Form::prepare("chat-post") . '
	<p>
		<input id="chat_message" type="text" name="chat_message" value="' . $_POST['chat_message'] . '" placeholder="Chat something here . . ." style="width:100%; box-sizing:border-box;" maxlength="200" tabindex="10" autofocus autocomplete="off" />
	</p>
	<input type="submit" name="submit" value="Post Chat" style="display:none;" />
	<input id="chat_username" type="text" name="chat_username" value="' . (isset(Me::$vals['handle']) ? Me::$vals['handle'] : '') . '" style="display:none;" />
	</form>';
}

echo '
	<input id="chat_channel_id" type="hidden" name="chat_channel_id" value="' . $channelID . '" />
	<input id="chat_time" type="hidden" name="chat_time" value="' . microtime(true) . '" />
</div>';

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");