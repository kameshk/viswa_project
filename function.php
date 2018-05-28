<?php
	//function file
	function getHMS($date1, $date2){	//Date format should be -> 2008-12-13 10:42:00
		$seconds = strtotime($date2) - strtotime($date1);

		$days    = floor($seconds / 86400);
		$hours   = floor(($seconds - ($days * 86400)) / 3600);
		$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
		$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
		
		return str_pad(($days*24)+$hours, 2, "0", STR_PAD_LEFT).":".str_pad($minutes, 2, "0", STR_PAD_LEFT).":".str_pad($seconds, 2, "0", STR_PAD_LEFT);
	}
	
//	echo getHMS("2018-05-01 00:00:00", "2018-05-01 00:02:00");

	function getVinListArray(){
		$db_connect	=	mysqli_connect(DB_HOST, DB_NAME, DB_PASS, DB_NAME);
		if(!$db_connect) die('Unable to connect to database due to '.mysqli_connect_error().'. Please contact the administrator.');
	
		$query	=	"select distinct(vin) as vin from data_table_1 order by vin asc";
		$exec	=	mysqli_query($db_connect, $query);
		$result_array	=	array();
		$result_array[]	=	'All';
		while($result	=	mysqli_fetch_array($exec)){
			$result_array[]	=	$result['vin'];
		}
		return $result_array;
	}
	
	function getTypeListArray(){
		$type_array	=	array();
	//	$type_array[]	=	'';
		$type_array[]	=	'Cumulative';
	//	$type_array[]	=	'Daily';
		return $type_array;
	}
?>