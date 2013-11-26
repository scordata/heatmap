<?php
//get host info to create dbase
$x= $_SERVER['HTTP_HOST'];
$host = str_replace(".","_",$x);
$host = str_replace("www_","", $host);
$host = $host."_heatmap";

$link=mysqli_connect("websitecentralsy.db.9556893.hostedresource.com","websitecentralsy","MarlaMap!s2088","websitecentralsy");


$ins = "SELECT Place, AVG(hereNow) FROM $host GROUP BY Place";
$res = mysqli_query($link, $ins) or die(mysql_error());

while($row = mysqli_fetch_array($res)){
	echo "The average visitors for ".$row['Place']. " is: ".$row['AVG(hereNow)'];
	echo "<br />";
}



?>