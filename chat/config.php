<?php

/*
	This config.php file should be located at /{application}/config.php. This file ONLY affects configurations for the
	application that it is stored in. If you want to change configurations across your entire server, you need to edit
	the /global-config.php file one level up.
	
	If you have different configurations that apply	depending on which environment you're currently using (such as your
	localhost environment vs. your production environment), you can set those configurations in the corresponding
	"local" and "production" sections.
	
	You can also override any configurations that were set by global-config.php here.
*/


/****************************************
****** Special Setup for Chat Sync ******
****************************************/

$host = explode(".", $_SERVER['SERVER_NAME']);

if(strpos($host[0], "pchat") !== false)
{
	$chatServer = "pchat" . (int) str_replace("pchat", "", $host[0]);
}
else
{
	$chatServer = "chat" . (int) str_replace("chat", "", $host[0]);
}

/**********************************************
****** Global Application Configurations ******
**********************************************/

// Set a Site-Wide Salt between 60 and 68 characters
// NOTE: Only change this value ONCE after installing a new copy. It will affect all passwords created in the meantime.
define("SITE_SALT", "_XpfbGrn;Hh_8=~l=M0EFL3CW;2yhU:iuka.7s:8_Zya*pWj5~.wQ~d816oKbbthm~kr");
//					|    5   10   15   20   25   30   35   40   45   50   55   60   65   |

// Set a unique 10 to 22 character keycode (alphanumeric) to prevent code overlap on databases & shared servers
// For example, you don't want sessions to transfer between multiple sites on a server (e.g. $_SESSION['user'])
// This key will allow each value to be unique (e.g. $_SESSION['siteCode_user'] vs. $_SESSION['otherSite_user'])
define("SITE_HANDLE", "sync_" . $chatServer);

// Set the Application Path (in most cases, this is the same as CONF_PATH)
define("APP_PATH", CONF_PATH);

// Site-Wide Configurations
$config['site-name'] = ucfirst($chatServer) . " Sync";
$config['database']['name'] = "sync_" . $chatServer;


/***********************************
****** Production Environment ******
***********************************/
if(ENVIRONMENT == "production") {

	// Set Important URLs
	define("SITE_URL", "http://" . $chatServer . ".sync.unifaction.com");
	define("CDN", "http://cdn.unifaction.com");
	
	// Important Configurations
	$config['site-domain'] = $chatServer . ".sync.unifaction.com";		#production
	$config['admin-email'] = "info@unifaction.com";
}

/************************************
****** Development Environment ******
************************************/
else if(ENVIRONMENT == "development") {
	
	// Set Important URLs
	define("SITE_URL", "http://" . $chatServer . ".sync.phptesla.com");
	define("CDN", "http://cdn.phptesla.com");
	
	// Important Configurations
	$config['site-domain'] = $chatServer . ".sync.phptesla.com";		#development
	$config['admin-email'] = "info@phptesla.com";
}

/******************************
****** Local Environment ******
******************************/
else if(ENVIRONMENT == "local") {
	
	// Set Important URLs
	define("SITE_URL", "http://" . $chatServer . ".sync.test");
	define("CDN", "http://cdn.test");
	
	// Important Configurations
	$config['site-domain'] = $chatServer . ".sync.test";
	$config['admin-email'] = "info@unifaction.test";

}

// Base style sheet for this site
Metadata::addHeader('<link rel="stylesheet" href="' . CDN . '/css/unifaction-3col.css" />');