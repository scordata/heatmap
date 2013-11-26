<?php
$name = $_POST['name'];
$location = $_POST['location'];
$hereNow = $_POST['herenow'];
$myFile = "test.txt";

    //sets datetime for mysql
   	$ymd = date('Y-m-d ');
   	$hour = date(H);
   	$hour = $hour + 3;
   	$minSec = date(':i:s');
    $fa = $ymd.$hour.$minSec;  


//get host info to create dbase
$x= $_SERVER['HTTP_HOST'];
$host = str_replace(".","_",$x);
$host = str_replace("www_","", $host);
$host = $host."_heatmap";

//MySQL Section:
// Create connection
$con=mysqli_connect("websitecentralsy.db.9556893.hostedresource.com","websitecentralsy","MarlaMap!s2088","websitecentralsy");

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }


// Create site-specific table if not exists
$tabcreate="CREATE TABLE IF NOT EXISTS ".$host."(RecordNumber MEDIUMINT NOT NULL AUTO_INCREMENT, Place varchar(200), HereNow varchar(200), TimeStamp timestamp, PRIMARY KEY(RecordNumber))";
mysqli_query($con,$tabcreate);
// create login table if not exists
//$hostpass = $host . "users";
//$uspass = "CREATE TABLE IF NOT EXISTS ".$hostpass."(userID VARCHAR(25) PRIMARY KEY,password VARCHAR(25),privateInformation VARCHAR(50))";
//mysqli_query($con,$uspass);


  //insert statement
  $sql="INSERT INTO ".$host." (Place, HereNow) VALUES ('$name', '$hereNow')";

if (!mysqli_query($con,$sql))
  {
  die('Error: ' . mysqli_error($con));
  }

mysqli_close($con);



//$ins = "INSERT INTO checkins.bpcheckins (Place, HereNow) VALUES ('$name', '$hereNow')";
//$res = mysql_query($ins);


?>