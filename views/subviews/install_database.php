GarageSale has detected that you have not yet installed your system. If this is a mistake please delete the install_database.php file (views/subviews/install_database.php).
<br />
<br />
Otherwise, enter your database information below or enter your database information into app/config.php and click the corresponding button below.
<br /><br />
<?php

// load the configuration file
require_once('app/config.php');
$config = new GarageSale\Config();

// select the type of database to use
$db_config = $config->databases['mysql'];

/* ---------------------------------------
 * load values from the configuration file
 */



// Store submitted values
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['host'])){
	$posted = 1;
	$host = $_POST['host'];
	$database = $_POST['database'];
	$username = $_POST['username'];
	$password = $_POST['password'];
}else{
	// host of databse
	$host = $db_config['host'];
	// name of databse
	$database = $db_config['database'];
	// user name to login
	$username = $db_config['username'];
	// password to log in
	$password = $db_config['password'];
	// prefix to use for table namesf
	$prefix = $db_config['prefix'];
}

?>
<div>
	 <!--if POST submitted, update form with submitted values as default values-->
	<form action="<?php echo $app->form_path('install'); ?>" method="POST" name = "database_form">
		<label>Host: <input type="text" name="host" placeholder="Database host name here."  
			<?php if(isset($posted)){echo 'value=' . $host;}?>></label>
		<br />
		<label>Database: <input type="text" name="database" placeholder="Database name here."
			<?php if(isset($posted)){echo 'value=' . $database;}?>></label>
		<br />
		<label>Username: <input type="text" name="username" placeholder="Database username here."
			<?php if(isset($posted)){echo 'value=' . $username;}?>></label>
		<br />
		<label>Password: <input name="password" placeholder="Database password here."
			<?php if(isset($posted)){echo 'value=' . $password;}?>></label>
		<br />
		<br />
		<input type="submit" name="submit" value="Create Tables &raquo;">
		
	</form>
</div><!--End of form container-->

<div>
	 <!--if POST submitted, update form with submitted values as default values-->
	<form action="<?php echo $app->form_path('install'); ?>" method="POST">
		<input type="submit" name="submit" value="Create Tables using config.php &raquo;">
	</form>
</div><!--End of form container-->

<script>
	var databaseValidator  = new Validator("database_form");
	databaseValidator.addValidation("host","req","Please provide the host!");
	databaseValidator.addValidation("database","req","Please provide the database!");
	databaseValidator.addValidation("username","req","Please provide the username!");
	databaseValidator.addValidation("password","req","Please provide the password!");
</script><!--End of validator script used to ensure all required fields are provided -->
	
<?php
	echo '<br />';
	if($_SERVER['REQUEST_METHOD'] === 'POST' ){
		echo 'Website install starting...<br /><br />';
		$con=mysqli_connect($host,$username,$password,$database);
		
		if (mysqli_connect_errno()) // Check connection
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}else{ // Start creating tables
			
			// *************Activation table*************
			$sql="CREATE TABLE `gs2_activation` (
  `userid` int(11) NOT NULL DEFAULT '-1',
  `hash` varchar(45) NOT NULL DEFAULT '-1',
  `code` varchar(11) NOT NULL DEFAULT '-1',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`,`userid`),
  UNIQUE KEY `userid_UNIQUE` (`userid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'activation' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Blocks table*************
			$sql="CREATE TABLE `gs2_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `blockedid` int(11) NOT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `blockedid` (`blockedid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'blocks' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Categories table*************
			$sql="CREATE TABLE `gs2_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `description` text,
  `parentid` int(11) DEFAULT '0',
  `category_order` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'categories' created successfully<br/>";
			}else{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Comments table*************
			$sql="CREATE TABLE `gs2_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listingid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL,
  `comment` text,
  `status` int(11) DEFAULT '0',
  `reputation` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `listingid` (`listingid`),
  KEY `userid` (`userid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'comments' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Contact table*************
			$sql="CREATE TABLE `gs2_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `subject` varchar(45) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `reason` varchar(45) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)) ";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'contact' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Listings table*************
			$sql="CREATE TABLE `gs2_listings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `description` text,
  `asking_price` float DEFAULT NULL,
  `other_offer` text,
  `title` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0',
  `categoryid` int(11) DEFAULT '1',
  `image_paths` text,
  `keywords` text,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `categoryid` (`categoryid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'listings' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Logged in users table*************
			$sql="CREATE TABLE `gs2_logged_in_users` (
  `id` int(11) NOT NULL,
  `uid` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'logged_in_users' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Messages table*************
			$sql="CREATE TABLE `gs2_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromid` int(11) NOT NULL,
  `toid` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0',
  `message` text,
  `subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fromid` (`fromid`),
  KEY `toid` (`toid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'messages' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Offers table*************
			$sql="CREATE TABLE `gs2_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `listingid` int(11) DEFAULT NULL,
  `offer_price` float DEFAULT NULL,
  `offer_other` text,
  `accepted` int(11) DEFAULT '0',
  `comment` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '0',
  `best_offer` int(11) DEFAULT '0',
  `review_submitted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `listingid` (`listingid`)) ";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'offers' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Profiles table*************
			$sql="CREATE TABLE `gs2_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` blob,
  `userid` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `major` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'profiles' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Reports table*************
			$sql="CREATE TABLE `gs2_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `offender` varchar(45) DEFAULT NULL,
  `explanation` varchar(45) DEFAULT NULL,
  `reason` varchar(45) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)) ";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'reports' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Reviews table*************
			$sql="CREATE TABLE `gs2_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `reviewerid` int(11) DEFAULT NULL,
  `description` text,
  `rating` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `listingid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `reviewerid` (`reviewerid`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'reviews' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
			
			// *************Users table*************
			$sql="CREATE TABLE `gs2_users` (
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `handle` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `usertype` varchar(20) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userlevel` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`))";
			if (mysqli_query($con,$sql))
			{
				echo "Table 'users' created successfully<br/>";
			}else
			{
				echo "Error creating table: " . mysqli_error($con)  . "<br/>";
			}  // End of else
		} // End of else
		echo '<br/>';
		echo "Website installation complete!";
		mysqli_close($con);
	} // End of if
?>
