<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare Values
$entryID = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['entry_id']) ? (int) $_POST['entry_id'] : 0);

// Form Submission
if(Form::submitted("add-featured-cont"))
{
	// Validate the input sent
	FormValidate::variable("Hashtag", $_POST['hashtag'], 0, 22);
	FormValidate::variable("Category", $_POST['category'], 1, 16);
	FormValidate::variable("Verb", $_POST['verb'], 1, 16);
	
	$_POST['entry_id'] = (int) $_POST['entry_id'];
	
	// Check if the entry exists
	if(!$entryData = AppFeatured::getEntryByID($_POST['entry_id']))
	{
		Alert::error("Invalid Entry", "That entry does not exist.");
	}
	
	if(FormValidate::pass())
	{
		Database::startTransaction();
		
		// Assign the featured content
		$pass = AppFeatured::assign($entryID, $_POST['hashtag'], $_POST['category'], $_POST['verb']);
		
		if(Database::endTransaction($pass))
		{
			Alert::saveSuccess("Content Assigned", "You have assigned the featured entry.");
			
			header("Location: /admin/AppFeatured/Assign Entries"); exit;
		}
		else
		{
			Alert::saveError("Assignment Failed", "An error occurred while trying to assign a featured entry.");
		}
	}
}

// Sanitize Post Values
else
{
	$_POST['category'] = isset($_POST['category']) ? Sanitize::variable($_POST['category']) : "";
	$_POST['verb'] = isset($_POST['verb']) ? Sanitize::variable($_POST['verb']) : "";
	$_POST['hashtag'] = isset($_POST['hashtag']) ? Sanitize::variable($_POST['hashtag']) : "";
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<h3>Add a new Featured Content Widget Entry</h3>
<form class="uniform" action="/admin/AppFeatured/Assign Entries" method="post">' . Form::prepare("add-featured-cont") . '

<p><a href="/admin/AppFeatured/List Entries">Find Entries to Assign</a></p>

<p>
	<strong>Top-Tier Hashtag:</strong><br />
	# <input type="text" name="hashtag" value="' . $_POST['hashtag'] . '" style="width:120px;" />
	<div style="font-size:0.9em;">Featured content is only shown on the hashtag selected. Examples include: #NFL, #Roleplaying, #PCGaming, etc.<br />Note: Leaving the hashtag blank allows it to appear on the UniFaction home page.</div>
</p>

<p>
	<strong>Category:</strong><br />
	<select name="category">' . str_replace(' value="' . $_POST['category'] . '"', ' value="' . $_POST['category'] . '" selected', '
		<option value="">-- Choose a Category --</option>
		<option value="articles">Articles</option>
		<option value="blogs">Blogs</option>
		<option value="communities">Communities</option>
		<option value="discussions">Discussions</option>
		<option value="games">Games</option>
		<option value="hashtags">Hashtags</option>
		<option value="links">Links</option>
		<option value="people">People</option>
		<option value="purchases">Purchases</option>
		<option value="sites">Sites</option>
	</select>') . '
</p>

<p>
	<strong>Verb:</strong><br />
	<select name="verb">' . str_replace(' value="' . $_POST['verb'] . '"', ' value="' . $_POST['verb'] . '" selected', '
		<option value="">-- Choose a Verb --</option>
		<option value="fun">Fun</option>
		<option value="interesting">Interesting</option>
		<option value="featured">Featured</option>
		<option value="popular">Popular</option>
		<option value="useful">Useful</option>
	</select>') . '
</p>

<p>
	<strong>Entry ID:</strong><br />
	<input type="text" name="entry_id" value="' . $entryID . '" />
</p>

<p><input type="submit" name="submit" value="Update" /></p>
</form>';

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
