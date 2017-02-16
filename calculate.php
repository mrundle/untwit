<?php
function Calculate_Annoying_Score($screenName){
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

	$friendIDs = [];
	//get your friends ID's
	$sql = "SELECT FriendID, FriendName FROM follow, user WHERE follow.UID = user.UID AND user.Name = '" . $screenName . "'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$friendID = $row["FriendID"];
			$friendName = $row["FriendName"];
			$friendIDs[$friendID] = $friendName;
    		}
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
	}
	

	//get your friends tweetIDs
	foreach ($friendIDs as $friendID => $friendName){
		$annoyingScore = 0;
		$tweetIDs = [];
		$sql = "SELECT TweetID FROM tweet WHERE tweet.FriendID = " . $friendID;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($tweetIDs, $row["TweetID"]);
	    		}
		} else {
			//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
		}

		//get the words in the tweet
		foreach ($tweetIDs as $tweetID){
			$words = [];
			$sql = "SELECT TextString, Count FROM word_usage WHERE word_usage.TweetID = " . $tweetID;
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$words[$row["TextString"]] = $row["Count"];
		    		}
			} else {
				//echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
			}
			
			//calculate annoying score
			foreach ($words as $word => $count){
				$sql = "SELECT * FROM dictionary WHERE word = '" . addslashes($word) . "'";
				$result = $conn->query($sql);
				if ($result->num_rows == 0) {
					$annoyingScore = $annoyingScore + 1;	
				}
			}
		}
		$annoyingScoreHash[$friendName] = $annoyingScore;
	}
#	print_r($annoyingScoreHash);
	return $annoyingScoreHash;
}


