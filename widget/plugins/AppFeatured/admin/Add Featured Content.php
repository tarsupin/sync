<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Form Submission
if(Form::submitted("add-featured-cont"))
{
	// Validate the input sent
	FormValidate::safeword("Title", $_POST['title'], 3, 22, "'?");
	FormValidate::safeword("Caption", $_POST['description'], 20, 72, "'?");
	FormValidate::url("URL", $_POST['url'], 10, 100);
	
	if(!$_FILES['image'] or !$_FILES['image']['tmp_name'])
	{
		Alert::error("Invalid Image", "You must provide a valid image.");
	}
	
	if(FormValidate::pass())
	{
		// Prepare Values
		$pass = false;
		
		Database::startTransaction();
		
		// Add the featured content
		if($entryID = AppFeatured::createEntry($_POST['title'], $_POST['description'], $_POST['url']))
		{
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
				
				$imagePath = APP_PATH . "/assets/featured/" . ceil($featuredID / 1000) . '/' . $featuredID . ".jpg";
				
				// Save the image
				$image->autoCrop(72, 72);
				$pass = $image->save($imagePath);
			}
		}
		
		if(Database::endTransaction($pass))
		{
			Alert::saveSuccess("Content Added", "You have added the featured content.");
			
			header("Location: /admin/AppFeatured/Add Featured Content"); exit;
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
	$_POST['title'] = isset($_POST['title']) ? Sanitize::safeword($_POST['title'], "'?") : "";
	$_POST['description'] = isset($_POST['description']) ? Sanitize::safeword($_POST['description'], "'?") : "";
	$_POST['url'] = isset($_POST['url']) ? Sanitize::url($_POST['url']) : "";
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<h3>Add a new Featured Content Widget Entry</h3>
<form class="uniform" action="/admin/AppFeatured/Add Featured Content" method="post" enctype="multipart/form-data">' . Form::prepare("add-featured-cont") . '

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
