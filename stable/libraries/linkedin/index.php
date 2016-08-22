<?php
session_start();

if(!extension_loaded('oauth')) {
	echo "You should install PECL lib OAuth";
	exit;
}
	
include('config.php');
require_once('Request.php');
require_once('Profile.php');
require_once('Connection.php');
require_once('Status.php');

/* Startup the autorisation precess and create an communication channel */
$linkedIn = new LinkedIn_Request();

/* Get the profile information */
$profile = $linkedIn->pullProfile($profileFields);

/* Get all connections from the profile */
$connections = $linkedIn->pullConnections();

// $linkedIn->publishStatus("First test of setting the status thru API");
?>
<h1>Profile</h1>
<?php echo $profile->firstname; ?>&nbsp;<?php echo $profile->lastname; ?>
<?php echo $profile->headline; ?>
<?php echo $profile->currentstatus; ?>

<h1>Connections</h1>
<?php
foreach($connections As $connection)
{
	echo $connection->getFirstname() . ' ' . $connection->getLastname();
	echo "<br/>";
	echo $connection->getHeadline();
	echo "<hr>";
}
?>