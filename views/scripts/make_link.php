<?php
$dataB = 'db30907';

$link=mysql_connect("mysql.cs.iastate.edu","u30907",$senha_nobody,$dataB)or
        die ("Unable to connect!! ");

// Check connection
if ($link == FALSE)
  {
  echo "Failed to connect to MySQL: " . mysql_connect_error();
  }
else{
   echo 'Connection to database successful';
  }
mysql_select_db($dataB, $link); //Selects desired database

?>



