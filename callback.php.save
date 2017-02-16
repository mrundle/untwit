<html>
<head>
</head>
<body>
<h1> Thank you! </h1>
<hr />
<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */
ini_set('display_errors',1);

/* Start session and load lib */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./clearsessions.php');
}

/* Connect to our Database */
$servername = "localhost";
$username = "amarti36";
$password = "andresl4";
$dbname = "mmahan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  //header('Location: ./index.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./clearsessions.php');
}

// Build necessary information
$account = $connection->get('account/verify_credentials');
$account = get_object_vars($account); // convert to array
//	account variable now contains:
//	$account['friends_count']
//	$account['followers_count']
//	$account['id']
//	$account['id_str']
//	$account['lang'] (= "en", for example)
//	$account['name']
//	$account['profile_image_url']
//	$account['screen_name']
//	...
//	(many more)
//	...
$info = array('user_id' => $account['id']);
$friends = $connection->get('friends/ids',$info);
$friends = get_object_vars($friends);
$friends = $friends['ids'];

//screenname and user id
$screenName = $account['screen_name'];
$UID = $account['id'];

//add the screen name and id of the user to the database
$sql = "INSERT INTO user (UID, Name) VALUES (" . $UID . ", '" . $screenName . "')";
if ($conn->query($sql) === TRUE) {
    //echo "New record created successfully";
} else {
    //echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
}

// We're now going to build a structure of all a users friends' tweets
$max_tweets = 50; 	// the number of tweets we want to get from each friend
// $friends_tweets = array(); // was overflowing memory
foreach($friends as $friend){

	while (True) {

		// retrieve the friends tweets
		$tweets = $connection->get('statuses/user_timeline',array('user_id' => $friend,'count' => $max_tweets));
	
		// check for rate limiting
		if ($connection->http_code === 429) {
			sleep(901);
		} else {
			break;
		}

	}

	$tweets = json_decode(json_encode($tweets),true); // converts to an array
	//$friends_tweets[$friend] = $tweets;

	$sql = "INSERT INTO follow (UID, FriendID) VALUES (" . $UID . ", " . $friend . ")";
	if ($conn->query($sql) === TRUE) {
		//echo "New record created successfully<br>";
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
	}

	echo '<h3>UserID: ' . $friend . '</h3>';

	//go through all of the tweets
	foreach($tweets as $twt){
		//get the date of the tweet and add it to the database
		$dateArray = explode(" ", $twt['created_at']);
		$year = $dateArray[5];
		$month = $dateArray[1];
		if( $month == "Jan" ) $month = "01";
		elseif( $month == "Feb" ) $month = "02";
		elseif( $month == "Mar" ) $month = "03";
		elseif( $month == "Apr" ) $month = "04";
		elseif( $month == "May" ) $month = "05";
		elseif( $month == "Jun" ) $month = "06";
		elseif( $month == "Jul" ) $month = "07";
		elseif( $month == "Aug" ) $month = "08";
		elseif( $month == "Sep" ) $month = "09";
		elseif( $month == "Oct" ) $month = "10";
		elseif( $month == "Nov" ) $month = "11";
		elseif( $month == "Dec" ) $month = "12";
		$day = $dateArray[2];
		$mTime = $dateArray[3];
		$sql = "INSERT INTO tweet (TweetID, TweetTimestamp, FriendID) VALUES (" . $twt['id'] . ", '" . $year . "-" . $month . "-" . $day . " " . $mTime  . "', " . $friend . ")";
		if ($conn->query($sql) === TRUE) {
			//echo "New record created successfully<br>";
		} else {
			//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
		}

		//check to see if the tweet has already been added to the database
		$sql = "SELECT * FROM word_usage WHERE TweetID = " . $twt['id'];
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			//echo "Tweet Exists<br>";
		} else {

			//check to see if the tweet is a retweet
			$twtArray = explode(" ", $twt['text']);
			if ($twtArray[0] == "RT"){

				array_shift($twtArray);
				$mentionID = array_shift($twtArray);
				$mentionID = substr($mentionID, 1);
				$mentionID = substr($mentionID, 0, -1);
				$mentionID = $connection->get('users/show',array('screen_name' => $mentionID));
				$mentionID = json_decode(json_encode($mentionID),true);
				$mentionID = $mentionID['id'];

				$sql = "INSERT INTO interaction (TweetID, Type, InteractorID) VALUES (" . $twt['id'] . ", " .  "'retweet', " . $mentionID . ")"; 
				if ($conn->query($sql) === TRUE) {
					// echo "New record created successfully<br>";
				} else {
					//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
				}
			}
			//if it does not exist go through each word in the tweet
			foreach($twtArray as $text){

				//if it is a mention add it as an interaction
				if (substr($text, 0, 1) == "@"){
					$mentionID = substr($text, 1);
					if (ctype_alnum(substr($mentionID, -1)) != TRUE) {
						$mentionID = substr($mentionID, 0, -1);
					}
					$mentionID = $connection->get('users/show',array('screen_name' => $mentionID));
					$mentionID = json_decode(json_encode($mentionID),true);
					$mentionID = $mentionID['id'];
					$sql = "INSERT INTO interaction (TweetID, Type, InteractorID) VALUES (" . $twt['id'] . ", " .  "'mention', " . $mentionID . ")"; 
					if ($conn->query($sql) === TRUE) {
						// echo "New record created successfully<br>";
					} else {
						//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
					}
				}

				//add the word into the database
				$sql = "INSERT INTO word_usage (TweetID, TextString, Count) VALUES (" . $twt['id'] . ", '" . addslashes($text) . "', " . 1 . ")"; 
				if ($conn->query($sql) === TRUE) {
					//echo "New record created successfully<br>";
				} else {//if the word is not in the database then increment the count for that word
					$sql = "UPDATE word_usage SET Count = Count + 1 WHERE TweetID = " . $twt['id'] . " AND TextString = '" . addslashes($text) . "'";
					if ($conn->query($sql) === TRUE) {
						// echo "Count++<br>";
					} else {
						//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
					}
				}
			}
		}
		// echo '<p>' . $twt['text'] . '<p>';
	}
	echo '<hr />';
}

?>

<p>Welcome!</p>
</body>
<html>
