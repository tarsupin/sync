<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// Secondary Navigation
WidgetLoader::add("SidePanel", 30, '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot"><a href="/user-panel/friends">Friends<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/sites">Sites Visited<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/">Account Settings<span class="icon-circle-right nav-arrow"></span></a></li>
	</ul>
</div>');
