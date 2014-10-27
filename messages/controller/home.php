<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }


// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

echo '
<div id="content">' . Alert::display();

AppMessages::sendMessage(1, 14, "Time: " . date("M jS, Y", mt_rand(0, 1500000000)));
AppMessages::sendMessage(14, 1, "There is something to consider here... perhaps it is time to look at that thing. Time: " . microtime(true));
AppMessages::sendMessage(23, 1, "Time: " . microtime(true));

$messages = AppMessages::getMessages(1, 14);

var_dump($messages);

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
