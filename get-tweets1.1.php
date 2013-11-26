<?php
 header('Refresh: 60');
//Configured by Adam Najman
//Script from webdevdoor.com
session_start();
require_once("twitteroauth-master/twitteroauth/twitteroauth.php"); //Path to twitteroauth library
 
//$twitteruser = "adamnajman";

/*
Modify $search to change what you'd like to search for
*/
$search = "hungry&geocode=40.752816,-73.984059,1mi";
$notweets = 30;
$consumerkey = "DfbhdkvwdKIPMFTjLBww"; //DO NOT CHANGE
$consumersecret = "IRsw7iatKyXNdPlHv3POaEjgV5qtFJEEfoPX4D28uI"; // DO NOT CHANGE
$accesstoken = "1527883435-ICanS9MQhYqd7yYu4lDVhOBJDcAzv19eoFGiXLG"; //DO NOT CHANGE
$accesstokensecret = "j2AgRlJ6b5WK1NtO4DZiA2F1c8fP172J9yLbUi8JbyU"; //DO NOT CHANGE
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
  
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

//test
//$content = $connection->get('account/verify_credentials');
//print_r($content);
//echo "<br />above is content<br />";
//$foo = "This is a test";
//$test = $connection->post('statuses/update', array('status' => $foo));
//print_r($test);
//echo "<br />above is test </br>";

$fetchurl = "https://api.twitter.com/1.1/search/tweets.json?q=".$search."&count=".$notweets;
 //$tweets is a JSON
$tweets = $connection->get($fetchurl);

for ($i=0; $i<count($tweets->statuses); $i++){
echo $tweets->statuses[$i]->user->name.":<br />";
echo $tweets->statuses[$i]->text."<br /><br />";
};

$content = $connection->get('account/verify_credentials');
//print_r($content);
//echo "<br />above is content<br />";

for($i=0; $i<count($tweets->statuses); $i++){
	$foo = $tweets->statuses[$i]->user->name." you should check this out!";
	$test = $connection->post('statuses/update', array('status' => $foo));
};

//$foo = "This is a test";
//$test = $connection->post('statuses/update', array('status' => $foo));



//$posturl = "https://api.twitter.com/1.1/statuses/update.json?status=".$foo;

//$rMessage = $connection->post($posturl);

 //echo $tweets;
//print_r($tweets);
//$blah =  json_encode($tweets);
//print_r($blah);
?>