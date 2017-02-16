<?php
/**
 * @file
 * Based on a UID, finds similar friends.
 */
ini_set('display_errors',1);

$user_id = $argv[1];

// Computes the similarity score
function compute_score($friend_1, $friend_2, $tf, $term_list, $idf) {

	$sumxx = .0001;
	$sumxy = .0001;
	$sumyy = .0001;
	$x = .0001;
	$y = .0001;
	$count = 0;

	foreach($term_list as $term) {
		
		if (array_key_exists($term, $tf[$friend_1]) === True && array_key_exists($term, $idf) === True) {
			$x = $tf[$friend_1][$term]*$idf[$term];
			$count++;
		} else {
			$x = 0;
		}
		if (array_key_exists($term, $tf[$friend_2]) === True && array_key_exists($term, $idf) === True) {
			$y = $tf[$friend_2][$term]*$idf[$term];
			$count++;
		} else {
			$y = 0;
		}

		$sumxx += $x * $x;
		$sumxy += $x * $y;
		$sumyy += $y * $y;	
	
	}

	//echo "Total number of non-zero calculations: " . $count . "\n"; 

	return $sumxy/sqrt($sumxx*$sumyy);

}

// Returns highest score
function highest_score($scores) {

	// Initialize
	$max_score = array();
	$max_score['score'] = 0;
	$max_score['friend_1'] = "Nobody";
	$max_score['friend_2'] = "Nobody";
	// Loop through
	foreach ($scores as $friend_1 => $friend_1_array) {
		foreach ($friend_1_array as $friend_2 => $score) {

			if ($score > $max_score['score']) {
				$max_score['friend_1'] = $friend_1;
				$max_score['friend_2'] = $friend_2;
				$max_score['score'] = $score;
			}	

		}
	}

	return $max_score;

}

// Returns the pairs with the highest scores
function n_highest_scores($scores, $n) {

	// Initialize
	$result = array();

	// Iteratively call n_high_scores $n times
	for ($i = 1; $i <= $n; $i++) {
		$result[$i] = highest_score($scores);
		$scores[$result[$i]['friend_1']][$result[$i]['friend_2']] = 0;	
	}	

	return $result;
}

function find_similar_friends($user_id) {

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

	// Make term list
	$term_list = array();
	$sql = "SELECT distinct TextString FROM word_usage";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($term_list, addslashes($row["TextString"]));
    		}	
	} else {
		return "Error: No tweet data found.\n";
	}

	echo "Making friend list \n";

	// Make friend list
	$friend_list = array();
	$sql = "SELECT f.FriendID as FriendID, count(i.TweetID) as interaction_count FROM follow f, interaction i WHERE UID = \"" . $user_id . "\" and f.FriendID = i.InteractorID group by f.FriendID order by interaction_count desc limit 50";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($friend_list, $row["FriendID"]);
    		}	
	} else {
		return "Error: No friends found.\n";
	}

	echo "Done making friend list \n";

	// Make arrays
	$scores = array();	
	$tf = array();
	$idf = array();

	// Get term use by friend frequencies
	echo "Getting term frequencies \n";
	$sql = "SELECT sum(w.count) as word_count, t.FriendID as friend_id, w.TextString as term FROM word_usage w, tweet t, (select FriendID FROM follow WHERE UID = " . $user_id . ") f WHERE t.FriendID = f.FriendID and w.TweetID = t.TweetID group by t.FriendID, w.TextString";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			// Grab values
			$friend = $row["friend_id"];
			$term = addslashes($row["term"]);
			$count = $row["word_count"];

			// Create friend_id array
			if (array_key_exists($friend, $tf) === False) {
				$tf[$friend] = array();
			}

			// Assign value
			$tf[$friend][$term] = $count;
		}
	} else {
		return "Error: " . $conn->error . "\n";
	}

	echo "Done getting term frequencies \n";

	// Get inverse frequencies (number of friends divided by number of friends that use term)
	echo "Getting inverse term frequencies \n";
	
	$num_friends = count($friend_list);

	$sql = "SELECT count(t.FriendID) as count, w.TextString as term FROM word_usage w, tweet t, (select FriendID FROM follow WHERE UID = " . $user_id . ") f WHERE t.FriendID = f.FriendID and w.TweetID = t.TweetID group by w.TextString";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			// Grab values
			$term = addslashes($row["term"]);
			$count = $row["count"];

			// Assign value
			$idf[$term] = log($num_friends/$count);
		}
	} else {
		return "Error: " . $conn->error . "\n";
	}

	echo "Done getting inverse term frequencies. Count: " . count($idf) . "\n";

	// Loop through friend combinations
	foreach ($friend_list as $friend_1) {

		// Check if term-frequency data exists
		if (array_key_exists($friend_1, $tf) === False) {
			continue;
		}

		// create array
		$scores[$friend_1] = array();
	
		foreach ($friend_list as $friend_2) {

			// Check if term-frequency data exists
			if (array_key_exists($friend_2, $tf) === False) {
				continue;
			}

			// Check if friend_1 is friend_2
			if ($friend_1 === $friend_2) {
				continue;
			}

			// Check if score has been computed already
			if (array_key_exists($friend_2, $scores) === True) {
				if (array_key_exists($friend_1, $scores[$friend_2]) === True) {
					continue;
				}
			} 

			// Compute Score
			$scores[$friend_1][$friend_2] = compute_score($friend_1, $friend_2, $tf, $term_list, $idf);
			
		}
	}

	echo "Done calculating scores\n";

	$results = n_highest_scores($scores, 5);

	echo "Getting screen_names \n";

	// Get screen_names
	foreach ($results as $key => $result) {
		$sql = "SELECT f1.FriendID as friend_1_ID, f1.FriendName as friend_1_name, f2.FriendID as friend_2_ID, f2.FriendName as friend_2_name FROM follow f1, follow f2 WHERE f1.UID = \"" . $user_id . "\" and f2.UID = \"" . $user_id ."\" and f1.FriendID = \"" . $result['friend_1'] . "\" and f2.FriendID = \"" . $result['friend_2'] . "\"";
		$query_result = $conn->query($sql);
		if ($query_result !== False) {
			$row = $query_result->fetch_assoc();
			$results[$key]['friend_1_name'] = $row['friend_1_name'];
			$results[$key]['friend_2_name'] = $row['friend_2_name'];
		} else {
			return "Error: " . $conn->error . "\n";
		}
	}

	return $results;

}

echo print_r(find_similar_friends($user_id), True) . "\n";

?>
