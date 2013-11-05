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
//print_r($content);
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
	<title>Bowdoin alumni sorted by number of Twitter followers</title>
	
	<link rel="stylesheet" href="style.css">
	
	<!-- Bootstrap CDN: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">
	<!-- Bootstrap CDN: Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
	
</head>
<body>

<h1>Bowdoin alumni sorted by number of Twitter followers</h1>

<hr>

<table class="table-hover">
<? foreach($users as $n => $user): ?>
	<tr class="user" onclick="document.location = 'https://twitter.com/<?= $user->screen_name ?>';">
		<td class="user-rank"><?= $n+1 ?></td>
		<td class="user-pic"><img src="<?= $user->profile_image_url ?>"></td>
		<td class="user-name"><?= $user->name ?></td>
		<td class="user-handle">@<?= $user->screen_name ?></td>
		<td class="user-desc"><?= $user->description ?></td>
		<td class="user-followers"><?= number_format($user->followers_count) ?></td>
	</tr>
<? endforeach; ?>
</table>

</body>
</html>
