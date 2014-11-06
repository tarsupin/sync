<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<style>
	.entry-row {  }
	.entry-row:nth-child(even) { padding:2px; background-color:#eeeeee; }
	.entry-row:nth-child(odd) { padding:2px; background-color:#dddddd; }
	.entry-left { display:table-cell; vertical-align:top; }
	.entry-right { display:table-cell; vertical-align:top; padding-left:8px; }
</style>

<h3>List of Featured Widget Entries</h3>';

// Prepare Values
$widgetSync = URL::widget_sync_unifaction_com();

// Get the list of entries
$results = Database::selectMultiple("SELECT * FROM widget_featured LIMIT 0, 200", array());

foreach($results as $entry)
{
	echo '
	<div class="entry-row"><div class="entry-left"><a href="/admin/AppFeatured/Assign Entries?id=' . $entry['id'] . '"><img src="' . $widgetSync . '/assets/featured/' . ceil($entry['id'] / 1000) . '/' . $entry['id'] . '.jpg" /></a></div><div class="entry-right"><strong>' . $entry['title'] . '</strong><br />' . $entry['description'] . '<br /><span><a href="' . $entry['url'] . '">' . $entry['url'] . '</a></span></div></div>';
}

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
