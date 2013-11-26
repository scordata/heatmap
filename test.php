<?php

require_once("twitter.class.php");
$Twitter = new Twitter;

$results = $Twitter->searchResults("web development");

foreach( $results->entry as $result )
{
	echo "<h3><a href=\"". $result->author->uri ."\">". $result->author->name ."<a/></h3><img src=\"". $result->link[1]->attributes()->href ."\" style=\"float: left;\"><p>". $result->content."</p><div style=\"clear:both;\">&nbsp;</div>";
}

$trends = $Twitter->weeklyTrends();

foreach( $trends["trends"] as $date => $trends )
{
	echo "<h3>". $date ." trends</h3>";
	foreach( $trends as $k => $trend )
	{
		echo $trend["query"] ."<br />";
	}
}
?>
