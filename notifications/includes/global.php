<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// Navigation
WidgetLoader::add("MobilePanel", 30, '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot"><a href="/">Home Page<span class="icon-circle-right nav-arrow"></span></a></li>
	</ul>
</div>');