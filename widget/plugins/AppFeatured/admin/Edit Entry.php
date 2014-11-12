<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure the ID is sent
if(!isset($_GET['id']))
{
	header("Location: /admin");
}

// Prepare Values
$entryID = (int) $_GET['id'];

// Run Form
if(Form::submitted("widget-entry-edit"))
{
	// Update the featured entry
	Database::query("UPDATE widget_featured SET title=?, description=?, url=? WHERE id=? LIMIT 1", array($_POST['title'], $_POST['description'], $_POST['url'], $entryID));
	
	// Update the image of the featured entry
	if(isset($_FILES['image']) and $_FILES['image']['tmp_name'])
	{
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
			
			$imagePath = APP_PATH . "/assets/featured/" . ceil($entryID / 1000) . '/' . $entryID . ".jpg";
			
			// Save the image
			$image->autoCrop(72, 72);
			$pass = $image->save($imagePath);
		}
		
		Alert::saveSuccess("Image Updated", "You have successfully updated the widget and it's image.");
	}
	else
	{
		Alert::saveSuccess("Widget Updated", "You have successfully updated the widget.");
	}
	
	header("Location: /admin"); exit;
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<h3>List of Featured Widget Entries</h3>';

// Prepare Values
$widgetSync = URL::widget_sync_unifaction_com();

// Get the list of entries
$entryData = Database::selectOne("SELECT * FROM widget_featured WHERE id=? LIMIT 1", array($entryID));

echo '
<style>
	.forminput { box-sizing:border-box; width:100%; }
</style>
<img src="' . $widgetSync . '/assets/featured/' . ceil($entryData['id'] / 1000) . '/' . $entryData['id'] . '.jpg" />

<form class="uniform" action="/admin/AppFeatured/Edit Entry?id=' . $entryData['id'] . '" method="post" enctype="multipart/form-data">' . Form::prepare('widget-entry-edit') . '
	<p>
		<strong>Title:</strong>
		<input class="forminput" type="text" name="title" value="' . $entryData['title'] . '" maxlength="22" />
	</p>
	<p>
		<strong>Description:</strong>
		<input class="forminput" type="text" name="description" value="' . $entryData['description'] . '" maxlength="72" />
	</p>
	<p>
		<strong>URL:</strong>
		<input class="forminput" type="text" name="url" value="' . $entryData['url'] . '" maxlength="72" />
	</p>
	<p>
		<strong>New Image</strong><br />
		<input type="file" name="image" />
	</p>
	<p>
		<input type="submit" name="submit" value="Update Entry" />
	</p>
</form>';

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
