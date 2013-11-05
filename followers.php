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

//$content = $connection->get("lists/members.json?slug=alumni-students-1&owner_screen_name=bowdoincollege&cursor=-1");

$users = array();
$next_cursor = -1;
$i = 0;
while($next_cursor !== 0) {
	$page = $connection->get("lists/members.json?slug=alumni-students-1&owner_screen_name=bowdoincollege&cursor=".$next_cursor);
	$next_cursor = $page->next_cursor;
	$users = array_merge($users, $page->users);
	$i++;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Bowdoin alumni with most followers</title>
</head>
<body>

<ul>
<? 
//print_r($content);
foreach($users as $user) { 
	//print_r($user);
	print "<li><b>".$user->screen_name."</b> - ".$user->followers_count."</li>";
}
?>
</ul>

</body>
</html>
