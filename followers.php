<?

session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

date_default_timezone_set('America/New_York');

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

// fill in some gaps with my own list
$next_cursor = -1;
$i = 0;
while($next_cursor !== 0) {
	$page = $connection->get("lists/members.json?slug=bowdoin-alums&owner_screen_name=tophtucker&cursor=".$next_cursor);
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

// remove duplicates
$users = array_unique($users, SORT_REGULAR);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Bowdoin alumni* sorted by number of Twitter followers</title>
	
	<link rel="shortcut icon" href="polarchomp.png">
	
	<link rel="stylesheet" href="style.css">
	
	<!-- Bootstrap CDN: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">
	<!-- Bootstrap CDN: Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
	
</head>
<body>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45488007-1', 'toph.me');
  ga('send', 'pageview');

</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=342498109177441";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<img src="polarchase.png" style="margin-top:-20px;" align="right">

<h1>Bowdoin alumni* sorted by number of Twitter followers</h1>
<p>*Rather, people who have attended or currently attend. List mostly assembled by <a href="https://twitter.com/bowdoincollege" target="new">@BowdoinCollege</a>. Site assembled by <a href="https://twitter.com/tophtucker" target="new">@tophtucker</a>. <br/><small>Last updated <?=date('m/d/Y h:i:s a')?>.</small></p>
<div class="fb-like" data-href="http://toph.me/bowdointweeps" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>
<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<hr>

<table class="table-hover">
<? foreach($users as $n => $user): ?>
	<tr class="user">
		<td class="user-rank"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $n+1 ?></a></td>
		<td class="user-pic"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><img src="<?= $user->profile_image_url ?>" width="48" height="48"></a></td>
		<td class="user-name"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $user->name ?><? if($user->verified): ?> <img src="verified.png" class="verified" width="15" height="15"><? endif; ?></a></td>
		<td class="user-handle"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new">@<?= $user->screen_name ?></a></td>
		<td class="user-desc"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $user->description ?></a></td>
		<td class="user-followers"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= number_format($user->followers_count) ?></a></td>
		<td class="user-search-o"><a href="http://bowdoinorient.com/search?q=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on The Bowdoin Orient" target="new"><img src="o.png" width="16" height="16"></a></td>
		<td class="user-search-g"><a href="http://google.com/search?q=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on Google" target="new"><img src="g.png" width="16" height="16"></a></td>
		<td class="user-search-w"><a href="http://en.wikipedia.org/w/index.php?title=Special:Search&search=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on Wikipedia" target="new"><img src="w.png" width="16" height="16"></a></td>
	</tr>
<? endforeach; ?>
</table>

</body>
</html>
