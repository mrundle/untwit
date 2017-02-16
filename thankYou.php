<!DOCTYPE html>
<html>
<head>
    <title>Untwit</title>
    <title>Untwit</title>
    <!-- Include meta tag to ensure proper rendering and touch zooming -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Include bootstrap stylesheets -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<style>
    footer {
        line-height: 1.2;
    }
    body { background: #FFFFCC; }
    </style>
</head>

<body>
<div class="container">

    <div class="row">
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
    // ALSO, save 

    /* Request access tokens from twitter */
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

    /* Save the access tokens. Normally these would be saved in a database for future use. */
    $_SESSION['access_token'] = $access_token;

    /* Remove no longer needed request tokens */
    //unset($_SESSION['oauth_token']);
    //unset($_SESSION['oauth_token_secret']);

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
    $screen_name = $account['screen_name'];
    $uid = $account['id_str'];

    //add the screen name and id of the user to the database
    $sql = "SELECT * FROM user WHERE Name = '" . $screen_name . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $is_finished = false;
        foreach ($result as $row) {
            $finished = $row['Finished'];
            if($finished == 1){
				// hash the screen name and send to the analysis page
				$secret = '822b4c2c38e164d9895f5a714268e33e';
				$sn_hash = md5($secret.$screen_name);
                $uid_hash = md5($secret.$uid);
                header('Location: ./analysis.php?sn=' . $screen_name . '&sn_hash=' . $sn_hash . '&uid=' . $uid . '&uid_hash=' . $uid_hash);
			}
			else{
				 // Welcome the user.
				echo "<h2 class=\"text-center\">Welcome, " . $screen_name . "</h2>";
				// Notify the user that their data is still being processed.
				echo "<p class='text-center'>Thank you for using Untwit. We are still processing your Twitter data. Please stop by again later to view your results!</p>"; 
			}   
        }
    }else{
        echo "Thank you for using Untwit. We're now starting to process your Twitter data. Please stop by again a little later to view your results!"; 
        shell_exec("nohup php /var/www/html/cse30246f14/untwit/pull_tweets.php " . $_SESSION['oauth_token'] . " " . $_SESSION['oauth_token_secret'] . " " . $_REQUEST['oauth_verifier'] . " > /var/www/html/cse30246f14/untwit/log.txt 2>&1 &");
    }
    ?>
    </div>
</div>

</body>
