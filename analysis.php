<!DOCTYPE html>
<html>
<head>
    <title>Untwit</title>
    <title>Untwit</title>
    <!-- Include meta tag to ensure proper rendering and touch zooming -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified JavaScript 
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script> -->
    <!-- Include bootstrap stylesheets 
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Include -->
	<link rel="stylesheet" href="css/main.css">
    <style>
        footer {
            line-height: 1.2;
        }
        body { background: #FFFFCC; }
    </style>
	<!--Make sure username hasn't been tampered with-->
	<?php
		$secret = '822b4c2c38e164d9895f5a714268e33e';
		if(md5($secret.$_REQUEST['sn']) != $_REQUEST['sn_hash']){
			header('Location: ./clearsessions.php');
		}
        if(md5($secret.$_REQUEST['uid']) != $_REQUEST['uid_hash']){
            header('Location: ./clearsessions.php');
        }
        // INCLUDE OUR ADVANCED FUNCTIONS
		include 'calculate.php';
        include 'find_similar_friends.php';
	?>

<script>
$( document ).ready(function() {
    // Open the appropriate container
    $("#annoying-container").collapse('toggle');
    $("#similarity-container").css("visibility","hidden");

    $("#annoying-btn").click(function(e) {
         // show tool
        if($("#annoying-container").is(":visible")) return;
        else{
           $("#annoying-container").collapse('toggle');
           $("#annoying-container").css('visibility','visible');
        }
        // hide everything else
        if($("#similarity-container").is(":visible")){
            $("#similarity-container").collapse('toggle');
            $("#similarity-container").css('visibility','hidden');
        }
    });

    $("#similarity-btn").click(function(e) {
        // show tool
        if($("#similarity-container").is(":visible")) return;
        else{
            $("#similarity-container").collapse('toggle');
            $("#similarity-container").css("visibility","visible");
        }
        // hide everything else
        if($("#annoying-container").is(":visible")){
            $("#annoying-container").collapse('toggle');
            ("#annoying-container").css("visibility","hidden");
        }
    });

    $('.nav li a').on('click', function(e){

        e.preventDefault(); // prevent link click if necessary?

        var $thisLi = $(this).parent('li');
        var $ul = $thisLi.parent('ul');

        if (!$thisLi.hasClass('active'))
        {
            $ul.find('li.active').removeClass('active');
                $thisLi.addClass('active');
        }

    });

});
</script>

</head>

<body>
<div class="container">

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="javascript:;">Untwit</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
		<li class="active"><a href="javascript:;" data-toggle="pill" id='annoying-btn'>AnnoyingRank<span class="sr-only">(current)</span></a></li>
        <li><a href="javascript:;" data-toggle="pill" id='similarity-btn'>SimilarFriends</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
			<span class="glyphicon glyphicon-user" aria-hidden="true"></span><span class="caret"></span>
		  </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:;">Delete My Data</a></li>
            <li><a href="./clearsessions.php">Log Out</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="collapse container" id='annoying-container'>
<h2 class='text-center'>AnnoyingRank</h2>
<table id='annoyingTable' data-toggle="table" class="table table-bordered table-striped table-responsive">
    <thead>
			<tr>
				<th>Screen Name</th>
                <th>Annoying Score</th>
			</tr>
		</thead>
		<tbody id='feedTableBody'>
            <?php
	        $scores = Calculate_Annoying_Score($_REQUEST['sn']);
            if(!$scores){
                echo "[ Calculate failed. Error. ] ";
            }
            else{
                arsort($scores);
                foreach($scores as $screen_name => $score){
                    echo "<tr>";                    
                    echo "<th>";
                    echo $screen_name;
                    echo "</th>";
                    echo "<th>";
                    echo $score;
                    echo "</th>";
                    echo "</tr>";
                }
            }
	        ?>
		</tbody>
</table>
</div>

<div class="collapse container" id='similarity-container'>
<h2 class='text-center'>FriendSimilarity</h2>
</div>

</div>
</body>
