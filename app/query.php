<?php
//function querydb($user) {
    //?user=2
    
    //need to validate this input
    $user = $_GET['user'];
	$pass = intval($_GET['pass']);       
        
    
    /* connect to the db */
    $link = mysql_connect('mysql.cs.iastate.edu', 'u30907', 'axTrtSRJj') or die('Cannot connect to the DB');
    mysql_select_db('db30907', $link) or die('Cannot select the DB');  
    $query = "SELECT id FROM User WHERE username='".$user."' AND password='".$pass."'";
	
    $result = mysql_query($query, $link) or die('Errant query:  ' . $query);
     
    header('Content-type: text/xml');
    echo '<query>';
    while ($row = mysql_fetch_array($result)) {
        echo "<row>";
		
        //for some reason count($row) returns twice as many rows as their actually are
        for ($i = 0; $i < count($row) / 2; $i++) {
            if ($row[$i] === NULL) {
                echo "<column>NULL</column>";
            } else {
                echo "<column>$row[$i]</column>";
            }
        }
        echo "</row>";
    }
    echo '</query>';
    /* disconnect from the db */
    @mysql_close($link);

//}
?>