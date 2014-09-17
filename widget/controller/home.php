<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

/****** Page Configuration ******/
$config['canonical'] = "/";
$config['pageTitle'] = "UniFaction Widgets";		// Up to 70 characters. Use keywords.
Metadata::$index = false;
Metadata::$follow = false;

// Run Global Script
require(APP_PATH . "/includes/global.php");

/****** Display Header ******/
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Eliminate the side panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display Main Page
echo '
<div id="panel-right"></div>
<div id="content" style="overflow:hidden;">';

echo 'Widget Home Page';

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");