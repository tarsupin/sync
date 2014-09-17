<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Form Submission
if(Form::submitted("add-featured-cont"))
{
	// Validate the input sent
	FormValidate::variable("Hashtag", $_POST['hashtag'], 0, 22);
	FormValidate::variable("Category", $_POST['category'], 1, 16);
	FormValidate::variable("Verb", $_POST['verb'], 1, 16);
	
	FormValidate::safeword("Title", $_POST['title'], 3, 22, "'?");
	FormValidate::safeword("Caption", $_POST['description'], 20, 72, "'?");
	
	FormValidate::url("URL", $_POST['url'], 10, 100);
	
	if(!$_FILES['image'] or !$_FILES['image']['tmp_name'])
	{
		Alert::error("Invalid Image", "You must provide a valid image.");
	}
	
	if(FormValidate::pass())
	{
		Database::startTransaction();
		
		// Add the featured content
		$pass = Database::query("INSERT INTO widget_featured (category, hashtag, verb, title, description, url, date_created) VALUES (?, ?, ?, ?, ?, ?, ?)", array($_POST['category'], $_POST['hashtag'], $_POST['verb'], $_POST['title'], $_POST['description'], $_POST['url'], time()));
		
		if($pass)
		{
			$pass = false;
			$featuredID = Database::$lastID;
			
			// Initialize the image upload plugin
			$imageUpload = new ImageUpload($_FILES['image']);
			
			// Set your image requirements
			$imageUpload->maxHeight = 3000;
			$imageUpload->maxWidth = 3000;
			$imageUpload->maxFilesize = 1024 * 1500;			// 1.5 megabytes max
			$imageUpload->saveMode = Upload::MODE_OVERWRITE;
			
			// Save the image to a chosen path
			if($imageUpload->validate())
			{
				$image = new Image($imageUpload->tempPath, $imageUpload->width, $imageUpload->height, $imageUpload->extension);
				
				$imagePath = APP_PATH . "/assets/featured/" . $featuredID . ".jpg";
				$imageURL = SITE_URL . "/assets/featured/" . $featuredID . ".jpg";
				
				// Save the image
				$image->autoCrop(80, 80);
				$pass = $image->save($imagePath);
			}
		}
		
		if(Database::endTransaction($pass))
		{
			Alert::saveSuccess("Content Added", "You have added the featured content.");
			
			header("Location: /admin/FeaturedWidgetAPI/Add Featured Content"); exit;
		}
		else
		{
			Alert::saveError("Content Failed", "An error occurred while trying to add this featured content.");
		}
	}
}

// Sanitize Post Values
else
{
	$_POST['category'] = isset($_POST['category']) ? Sanitize::variable($_POST['category']) : "";
	$_POST['verb'] = isset($_POST['verb']) ? Sanitize::variable($_POST['verb']) : "";
	$_POST['hashtag'] = isset($_POST['hashtag']) ? Sanitize::variable($_POST['hashtag']) : "";
	$_POST['title'] = isset($_POST['title']) ? Sanitize::variable($_POST['title']) : "";
	$_POST['description'] = isset($_POST['description']) ? Sanitize::variable($_POST['description']) : "";
	$_POST['url'] = isset($_POST['url']) ? Sanitize::variable($_POST['url']) : "";
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<h3>Add a new Featured Content Widget Entry</h3>
<form class="uniform" action="/admin/FeaturedWidgetAPI/Add Featured Content" method="post" enctype="multipart/form-data">' . Form::prepare("add-featured-cont") . '

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
		<option value="popular">Popular</option>
		<option value="useful">Useful</option>
	</select>') . '
</p>

<p>
	<strong>Title:</strong><br />
	<input type="text" name="title" value="' . $_POST['title'] . '" style="width:90%;" maxlength="22" />
</p>

<p>
	<strong>Caption / Short Description:</strong><br />
	<input type="text" name="description" value="' . $_POST['description'] . '" style="width:90%;" maxlength="72" />
</p>

<p>
	<strong>URL to link to:</strong><br />
	<input type="text" name="url" value="' . $_POST['url'] . '" style="width:90%;" maxlength="100" />
</p>

<p>
	<strong>Image to Show (will be cropped into a square, 1:1 ratio):</strong><br />
	<input type="file" name="image" />
</p>

<p><input type="submit" name="submit" value="Update" /></p>
</form>';

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
