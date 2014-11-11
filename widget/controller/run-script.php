<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure only admins can run this page
if(Me::$clearance < 8 and ENVIRONMENT != "local")
{
	die("Unable to access this page.");
}

/******************************
****** Parent Structures ******
******************************/

// Sports
AppAdmin::createParent("Basketball", "Sports");
AppAdmin::createParent("Cricket", "Sports");
AppAdmin::createParent("Football", "Sports");
AppAdmin::createParent("Golf", "Sports");
AppAdmin::createParent("LaCrosse", "Sports");
AppAdmin::createParent("MLB", "Sports");
AppAdmin::createParent("NBA", "Sports");
AppAdmin::createParent("NCAAF", "Sports");
AppAdmin::createParent("NCAAM", "Sports");
AppAdmin::createParent("NFL", "Sports");
AppAdmin::createParent("NHL", "Sports");
AppAdmin::createParent("Rugby", "Sports");
AppAdmin::createParent("Tennis", "Sports");

// Basketball
AppAdmin::createParent("NBA", "Basketball");

// Football
AppAdmin::createParent("NFL", "Football");



// Entertainment
AppAdmin::createParent("Avatar", "Entertainment");
AppAdmin::createParent("Books", "Entertainment");
AppAdmin::createParent("Fashion", "Entertainment");
AppAdmin::createParent("Fitness", "Entertainment");
AppAdmin::createParent("Food", "Entertainment");
AppAdmin::createParent("Gaming", "Entertainment");
AppAdmin::createParent("Humor", "Entertainment");
AppAdmin::createParent("Music", "Entertainment");
AppAdmin::createParent("Movies", "Entertainment");
AppAdmin::createParent("Pets", "Entertainment");
AppAdmin::createParent("Relationships", "Entertainment");
AppAdmin::createParent("Shows", "Entertainment");
AppAdmin::createParent("Travel", "Entertainment");
AppAdmin::createParent("Writers", "Entertainment");


// Tech
AppAdmin::createParent("Electronics", "Tech");
AppAdmin::createParent("Gadgets", "Tech");
AppAdmin::createParent("Programming", "Tech");
AppAdmin::createParent("WebDev", "Tech");


// ContentCreation
AppAdmin::createParent("Art", "ContentCreation");
AppAdmin::createParent("Animating", "ContentCreation");
AppAdmin::createParent("Drawing", "ContentCreation");
AppAdmin::createParent("Blogs", "ContentCreation");
AppAdmin::createParent("Blogging", "ContentCreation");
AppAdmin::createParent("Design", "ContentCreation");
AppAdmin::createParent("Development", "ContentCreation");
AppAdmin::createParent("Painting", "ContentCreation");
AppAdmin::createParent("Programming", "ContentCreation");
AppAdmin::createParent("WebDev", "ContentCreation");
AppAdmin::createParent("Writers", "ContentCreation");
AppAdmin::createParent("Writing", "ContentCreation");


// DIY
AppAdmin::createParent("DIYAuto", "DIY");
AppAdmin::createParent("DIYCrafts", "DIY");
AppAdmin::createParent("DIYOutdoors", "DIY");
AppAdmin::createParent("DIYProducts", "DIY");
AppAdmin::createParent("HomeImprovement", "DIY");
AppAdmin::createParent("InteriorDesign", "DIY");


// CurrentEvents
AppAdmin::createParent("Business", "CurrentEvents");
AppAdmin::createParent("News", "CurrentEvents");
AppAdmin::createParent("Politics", "CurrentEvents");
AppAdmin::createParent("Science", "CurrentEvents");
AppAdmin::createParent("Tech", "CurrentEvents");



/***************************
****** Generate Lists ******
***************************/

// Entertainment
AppAdmin::assignEntryByTitle("Avatar Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Book Club", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Fashion Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Fitness Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Food Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Gaming Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Music Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Movie Fans", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Pet Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Relationships", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Tech Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Travel Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("TV and Web Shows", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Writing Community", "Entertainment", "communities", "featured");


// DIY
AppAdmin::assignEntryByTitle("DIY: Auto", "DIY", "communities", "featured");
AppAdmin::assignEntryByTitle("DIY: Home Improvement", "DIY", "communities", "featured");
AppAdmin::assignEntryByTitle("Interior Design", "DIY", "communities", "featured");

// ContentCreation
AppAdmin::assignEntryByTitle("Art Community", "ContentCreation", "communities", "featured");
AppAdmin::assignEntryByTitle("Programming Community", "ContentCreation", "communities", "featured");
AppAdmin::assignEntryByTitle("Web Development", "ContentCreation", "communities", "featured");
AppAdmin::assignEntryByTitle("Writing Community", "ContentCreation", "communities", "featured");


// CurrentEvents
AppAdmin::assignEntryByTitle("Business Community", "CurrentEvents", "communities", "featured");
AppAdmin::assignEntryByTitle("News Community", "CurrentEvents", "communities", "featured");
AppAdmin::assignEntryByTitle("Politics Community", "CurrentEvents", "communities", "featured");
AppAdmin::assignEntryByTitle("Science Community", "CurrentEvents", "communities", "featured");
AppAdmin::assignEntryByTitle("Tech Community", "CurrentEvents", "communities", "featured");


// Tech
AppAdmin::assignEntryByTitle("Book Club", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Gaming Community", "Entertainment", "communities", "featured");
AppAdmin::assignEntryByTitle("Music Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("News Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Politics Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Science Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Tech Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Travel Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Programming Community", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Web Development", "Tech", "communities", "featured");
AppAdmin::assignEntryByTitle("Writing Community", "Tech", "communities", "featured");

