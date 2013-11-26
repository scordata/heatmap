<?php
 header('Refresh: 600');
session_start();
?>


<html>
	<head>
		<title>OrphMedia's Foursquare Heat Map</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="stylesheet" href="style/style.css" />	
		
	</head>
	
	<body>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({

      appId      : '1389150617966048',                   // App ID from the app dashboard
      channelUrl : 'http://www.adamnajman.com', // Channel file for x-domain comms
      status     : true,                                 // Check Facebook Login status
      xfbml      : true                                  // Look for social plugins on the page
    });

    // Additional initialization code such as adding Event Listeners goes here
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
			<div id="wrapper">


<div id="fb-root"></div>
<div class="fb-like" data-href="http://adamnajman.com/heatmap/heatmap.php" data-send="true" data-width="450" data-show-faces="true"></div>

<div id="fb-root"></div>
	<div class="fb-follow" data-href="https://www.facebook.com/orphmedia" data-show-faces="true" data-width="450"></div>

<!-- BEGIN TWITTER SEARCH -->
<?php
	
	require_once("twitteroauth-master/twitteroauth/twitteroauth.php"); //Path to twitteroauth library

	/*
	Modify $search to change what you'd like to search for
	*/
	$term = "design";
	$latlong = "40.743648,-73.989193,1mi";

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
		$lat = "40.743648";
		$long = "-73.989193";
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
		$result = fetchData("https://api.instagram.com/v1/tags/webdeisgn/media/recent?client_id={$clientid}");
		$result = json_decode($result);
		//print_r($result);
	?>

	<div id="instagram">
	
	<?php  $i = 0; 
	foreach ($result->data as $post):
	?>
		<!-- Renders images. @Options (thumbnail,low_resoulution, high_resolution) -->
		<p><a id="instpic" href="<?= $post->images->high_resolution->url ?>"><img height=50 width=50 src="<?= $post->images->thumbnail->url ?>" /></a></p>
		<p id="instusr"><?=$post->user->username?></p>
		<p id="insttext">This is INSTAGRAM</p>
		<p id="insttext"><?=$post->caption->text?></p>
		<p id="twt">This is TWITTER</p>
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
	
	<script type="text/javascript" src="scripts/all-min.js"></script> </div>	
	<div id="fb-root"></div>
	<div class="fb-comments" data-href="http://adamnajman.com/heatmap/heatmap.php" data-width="470" data-num-posts="10"></div>
	<div id="fb-root"></div>
<div class="fb-facepile" data-href="https://www.facebook.com/orphmedia" data-app-id="1389150617966048" data-max-rows="1" data-width="300"></div>
	</div>	</div> <!-- ends wrapper -->
	<!-- END HEATMAP -->
	</body>
</html>
