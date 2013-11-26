
<html>
	<head>
		<title>OrphMedia's Foursquare Heat Map</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="stylesheet" href="style/style.css" />
	</head>
	
	<body>
			<div id="wrapper">
<!-- BEGIN TWITTER SEARCH -->
<?php
	 header('Refresh: 600');
	//Configured by Adam Najman
	//Script from webdevdoor.com
	session_start();
	require_once("twitteroauth-master/twitteroauth/twitteroauth.php"); //Path to twitteroauth library

	/*
	Modify $search to change what you'd like to search for
	*/
	$term = "bryant";
	$latlong = "40.752816,-73.984059,1mi";

	//$search = "nature&geocode=40.752816,-73.984059,1mi";
	$search = $term."&geocode=".$latlong;
	$notweets = 10;
	$consumerkey = "DfbhdkvwdKIPMFTjLBww"; //DO NOT CHANGE
	$consumersecret = "IRsw7iatKyXNdPlHv3POaEjgV5qtFJEEfoPX4D28uI"; // DO NOT CHANGE
	$twt_accesstoken = "1527883435-ICanS9MQhYqd7yYu4lDVhOBJDcAzv19eoFGiXLG"; //DO NOT CHANGE
	$accesstokensecret = "j2AgRlJ6b5WK1NtO4DZiA2F1c8fP172J9yLbUi8JbyU"; //DO NOT CHANGE
	 
	function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
	  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
	  return $connection;
	}
	  
	$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $twt_accesstoken, $accesstokensecret);



	$fetchurl = "https://api.twitter.com/1.1/search/tweets.json?q=".$search."&count=".$notweets;
	 //$tweets is a JSON
	$tweets = $connection->get($fetchurl);
	//print_r($tweets);
	/*
	echo "<div id='tweets'>";
	for ($i=0; $i<count($tweets->statuses); $i++){
		echo "<p id='twtpic'><img src=".$tweets->statuses[$i]->user->profile_image_url." /></p>";
		echo "<p id='usr'>".$tweets->statuses[$i]->user->name.":<p/>";
		echo "<p id='twt'>".$tweets->statuses[$i]->text."<p/>";
	};
	echo "</div>";*/

	$content = $connection->get('account/verify_credentials');


	//<!-- END TWITTER PULL -->
	//<!-- BEGIN INSTAGRAM STUFF -->
		// Supply a user id and an access token
		$clientid = "5a437c4f8dcc41d0b80c4dcddfeb2c6a";
		$userid = "235561461";
		$accessToken = "235561461.ab103e5.7662fa5e111b4d4d81fa521cc331da37";
		$lat = "40.752816";
		$long = "-73.984059";
		// Gets our data
		function fetchData($url){			
		     $ch = curl_init();
		     curl_setopt($ch, CURLOPT_URL, $url);
		     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		     $result = curl_exec($ch);
		     curl_close($ch); 
		     return $result;
		}

		// Pulls and parses data.
		$result = fetchData("https://api.instagram.com/v1/tags/bryantpark/media/recent?client_id={$clientid}");
		$result = json_decode($result);
		//print_r($result);
	?>

	<div id="instagram">
	<?php  $i = 0; 
	foreach ($result->data as $post):
	?>
		<!-- Renders images. @Options (thumbnail,low_resoulution, high_resolution) -->
		<p><a id="instpic" href="<?= $post->images->thumbnail->url ?>"><img src="<?= $post->images->thumbnail->url ?>"></a></p>
		<p id="instusr"><?=$post->user->username?></p>
		<p id="insttext"><?=$post->caption->text?></p>
		<!--<p><img src="<?= $tweets->statuses[$i]->user->profile_image_url ?>" /></p>-->
		<br /><br /><br /><br />
		<p id="usr"><?= $tweets->statuses[$i]->user->name ?>: <a href="http://www.twitter.com/<?= $tweets->statuses[$i]->user->screen_name ?>"><img id="twtpic" src="<?= $tweets->statuses[$i]->user->profile_image_url ?>" /></a><p/>
		<p id="twt"><?= $tweets->statuses[$i]->text ?><p/>
	<?php 
	$i++;
	if($i==10) break; endforeach 
	?>
	</div>
	<!-- END INSTAGRAM -->

	<!-- BEGIN HEATMAP -->
		<div id="map">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<!--<script type="text/javascript" src="scripts/__jquery.tablesorter/jquery.tablesorter.min.js"></script>

	<script type='text/javascript' src='jquery.fancybox-1.3.4.pack.js'></script>-->
	
	
	<script type="text/javascript" src="scripts/all-min.js"></script> </div>	</div> <!-- ends wrapper -->
	<!-- END HEATMAP -->
	</body>
</html>
