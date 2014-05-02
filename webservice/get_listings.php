<?php
//function querydb($user) {
    //?user=2
    
    //need to validate this input
    $page = intval($_GET['page']);
	$num_at_a_time = 15;    
	$start = $page * $num_at_a_time;
    //mysql_field_name ( resource $result , int $field_offset )
    /* connect to the db */
    $link = mysql_connect('mysql.cs.iastate.edu', 'u30907', 'axTrtSRJj') or die('Cannot connect to the DB');
    mysql_select_db('db30907', $link) or die('Cannot select the DB'); 
	
	$header_query = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='db30907' AND `TABLE_NAME`='gs_listings'";
	 
    $query = "SELECT * FROM gs_listings LIMIT $start,$num_at_a_time";
	
	$header_result = mysql_query($header_query, $link) or die('Errant query:  ' . $header_query);
    $result = mysql_query($query, $link) or die('Errant query:  ' . $query);
     
	 
	$header_array = array();
	$i = 0;
	while ($header_row = mysql_fetch_array($header_result)){
		$header_array[$i] = $header_row[0];
		$i++;
	}
	
	//echo $header_array[0];
	//echo $header_array[4];
	//echo $header_query;
	//echo $header_result;
	//$header_result = mysql_fetch_array($header_result);
	//echo $header_result[0];
	//$header_result = mysql_fetch_array($header_result);
    header('Content-type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo "<listings>";
    while ($row = mysql_fetch_array($result)) {
    	
        echo "<listing>";
		
        //for some reason count($row) returns twice as many rows as their actually are
        for ($i = 0; $i < count($row) / 2; $i++) {
            if ($row[$i] === NULL) {
                echo "<$header_array[$i]>NULL</$header_array[$i]>";
            }elseif($header_array[$i] == "userid"){
            	$query2 = "SELECT name FROM db30907.gs_users WHERE id='$row[$i]'";
				$result2 = mysql_query($query2, $link) or die('Errant query:  ' . $query2);
				$r_row = mysql_fetch_array($result2);
				echo "<$header_array[$i]>$row[$i]</$header_array[$i]>";
				echo "<username>$r_row[0]</username>";
			}else {
                echo "<$header_array[$i]>$row[$i]</$header_array[$i]>";
            }
        }
        echo "</listing>";
    }
    echo '</listings>';
    /* disconnect from the db */
    @mysql_close($link);

//}
?>
