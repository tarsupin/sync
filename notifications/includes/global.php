<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// Prepare Notifications (if available)
if(Me::$loggedIn)
{
	WidgetLoader::add("SidePanel", 1, Notifications::sideWidget());
}

// Main Navigation
$urlActive = (isset($url[0]) && $url[0] != "" ? $url[0] : "home");

WidgetLoader::add("SidePanel", 10, '
<div id="panel-navigation">
	<a class="panel-link' . ($urlActive == "home" ? " panel-active" : "") . '" href="/"><span class="icon-home panel-icon"></span><span class="panel-title">Home</span></a>
	<a class="panel-link' . ($urlActive == "post" ? " panel-active" : "") . '" href="/post"><span class="icon-edit panel-icon"></span><span class="panel-title">Post a Blog</span></a>
</div>');

// Secondary Navigation
WidgetLoader::add("SidePanel", 30, '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot"><a href="/user-panel/friends">Friends<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/sites">Sites Visited<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/">Account Settings<span class="icon-circle-right nav-arrow"></span></a></li>
	</ul>
</div>');

// Widgets

// Document List
WidgetLoader::add("SidePanel", 50, '
<div class="panel-box">
	<a href="#" class="panel-head">Documents<span class="icon-circle-right nav-arrow"></a>
	<ul class="panel-notes">
		<li class="nav-note"><a href="/docs/faqs">Frequently Asked Questions</a></li>
		<li class="nav-note"><a href="/docs/tos">Terms of Service</a></li>
		<li class="nav-note"><a href="/docs/privacy">Privacy Policy</a></li>
		<li class="nav-note"><a href="/">Contact Us</a></li>
	</ul>
</div>');