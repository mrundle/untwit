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
	<?php
	if(!isset($content_button)) header('Location: ./clearsessions.php');
	?>
</head>

<body>
<div class="container">
    <div class="row">
      <h2 class="text-center">Welcome to Untwit!</h2>
    </div>

    <div class="row">
      <p class="text-center"> We will look at your followers and decide which ones you should unfollow! </p>
      <p class="text-center"> We're going to need you to sign in to twitter to use our application. This allows us to read your tweets and see who you follow. </p>
    </div>

    <div class="row">
        <p class="text-center">
	    <?php print_r($content_button);?>
        </p>
    </div>

    <div class="row">
    <div class="text-center">
      <p> If you are having issues logging in, try <a href='./clearsessions.php'>clearing your session</a>.</p>
    </div>
    </div>

      <hr /> <!-- This makes a line on the page -->


    <footer>
    	<p class="text-center">Untwit makes use of Abraham Williams "twitteroauth" PHP library for working with Twitter's OAuth API. Check it out on <a = href='https://github.com/abraham/twitteroauth'>Github.</a></p>
    </footer>

</div>
<!-- JavaScript placed at the end of the document so the pages load faster -->
<!-- Optional: Include the jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Optional: Incorporate the Bootstrap JavaScript plugins -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
