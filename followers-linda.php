<?

session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

// instructions from here https://dev.twitter.com/docs/auth/oauth/single-user-with-examples#php
function getConnectionWithAccessToken($oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$connection = getConnectionWithAccessToken("5056501-ujO2WnCuxruqoDbUxPKzcEybDdSEJh5G39kBGkbj2F", "4SJgrmGwbTObWiUQwCPoaRZwAfJceFeg2kqWW1jPawjDU");
$content = $connection->get("lists/members.json?slug=the-best&owner_screen_name=tophtucker&cursor=-1");

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Some people and their follower counts</title>
</head>
<body>

<h1>Hi Linda!</h1>
<h2>Some people from my Twitter list of good twitterers, and their current follower counts.</h2>

<ul>
<? 
foreach($content->users as $user) { 
	print "<li><b>".$user->screen_name."</b> - ".$user->followers_count."</li>";
}
?>
</ul>

</body>
</html>
