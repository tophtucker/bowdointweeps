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
//$users = $content->users;

$users = array();

// load everything from first bowdoin alumni list
$next_cursor = -1;
$i = 0;
while($next_cursor !== 0) {
	$page = $connection->get("lists/members.json?slug=alumni-students-1&owner_screen_name=bowdoincollege&cursor=".$next_cursor);
	$next_cursor = $page->next_cursor;
	$users = array_merge($users, $page->users);
	$i++;
}

// load everything from second bowdoin alumni list
$next_cursor = -1;
$i = 0;
while($next_cursor !== 0) {
	$page = $connection->get("lists/members.json?slug=alumni-students-2&owner_screen_name=bowdoincollege&cursor=".$next_cursor);
	$next_cursor = $page->next_cursor;
	$users = array_merge($users, $page->users);
	$i++;
}

// compare follower counts to sort highest to lowest
function cmp_followers($a, $b)
{
	if ($a->followers_count == $b->followers_count) {
        return 0;
    }
    return ($a->followers_count > $b->followers_count) ? -1 : 1;
}

// sort users from highest follower count to lowest
usort($users, "cmp_followers");

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Bowdoin alumni with most followers</title>
</head>
<body>

<h1>Bowdoin alumni with most followers</h1>

<ol>
<? 
//print_r($users);
foreach($users as $user) { 
	//print_r($user);
	print "<li><b>".$user->screen_name."</b> - ".$user->followers_count."</li>";
}
?>
</ol>

</body>
</html>
