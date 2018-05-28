<?php
	//Get Page
	require_once __DIR__ . '/config/config.php';
	require_once __DIR__ . '/function.php';
	
	if(isset($_GET['data']) && $_GET['data'] == 'status'){
		$vin		=	null;
		$type		=	null;
		$fromdate	=	null;
		$todate		=	null;
		
		if(isset($_GET['vin']))		{	$vin		=	$_GET['vin'];		}
		if(isset($_GET['type']))	{	$type		=	$_GET['type'];		}
		if(isset($_GET['fromdate'])){	$fromdate	=	date('Y-m-d 00:00:00',strtotime($_GET['fromdate']));	}
		if(isset($_GET['todate']))	{	$todate		=	date('Y-m-d 23:59:59',strtotime($_GET['todate']));		}
		
		if($vin != 'All' ){
			$vin_filter	=	" && vin = '$vin'";
		}
		else{
			$vin_filter	=	null;
		}
		
		$gap_frequency_count		=	array();
		for($i=1;$i<=10000;$i++){
			$gap_frequency_count[]	=	15*$i;
		}
		$gap_frequency_count	=	implode("','",$gap_frequency_count);
		
		if($type == 'Cumulative'){
			$query	=	"SELECT vin, COUNT(id) as NoOfAPI, '".$_GET['fromdate']." to ".$_GET['todate']."' as ResponseDate,  SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN vehicle_mode NOT IN ('Drive', 'Idle', 'NC', 'FC') THEN 1 WHEN odometer<0 OR odometer>99999 THEN 1 WHEN speed<0 OR speed>100 THEN 1 WHEN soc<0 OR soc>100 THEN 1 WHEN dte<0 OR dte>140 THEN 1 WHEN ac_status NOT IN ('ON', 'OFF') THEN 1 ELSE 0 END) AS NoOfErrors from data_table_1 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin";
		}
		elseif($type == 'Daily'){
			$query	=	"SELECT vin, date(response_timestamp) as ResponseDate, COUNT(id) as NoOfAPI, SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN vehicle_mode NOT IN ('Drive', 'Idle', 'NC', 'FC') THEN 1 WHEN odometer<0 OR odometer>99999 THEN 1 WHEN speed<0 OR speed>100 THEN 1 WHEN soc<0 OR soc>100 THEN 1 WHEN dte<0 OR dte>140 THEN 1 WHEN ac_status NOT IN ('ON', 'OFF') THEN 1 ELSE 0 END) AS NoOfErrors from data_table_1 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin, date(response_timestamp)";
		}
		
	//	echo $query;
		echo <<<_END
		<h2>Status</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>	#							</th>
					<th>	VIN							</th>
					<th>	Period						</th>
					<th>	Total API					</th>
					<th>	Consecutive 15 Missing Data	</th>
					<th>	Max Data Missing Duration	</th>
					<th>	# of Errors					</th>
				</tr>
			</thead>
			<tbody>
_END;
		$exec	=	mysqli_query($db_connect, $query);
		$Sl		=	0;
		while($result	=	mysqli_fetch_array($exec)){
			$result_vin						=	$result['vin'];
			$result_noofapi					=	$result['NoOfAPI'];
			$result_15consecutingmissing	=	$result['15TotalConsecutiveMissing'];
			$result_maxmissingduration		=	$result['MaxMissingDuration'];
			$result_responsedate			=	$result['ResponseDate'];
			$result_nooferrors				=	$result['NoOfErrors'];
			$Sl								=	$Sl+1;
		//	echo $result_vin."|".$result_responsedate."|".$result_noofapi."|".$result_15consecutingmissing."|".$result_maxmissingduration."|".$result_nooferrors."</br>";
			echo '<tr>';
			echo '<td>'. $Sl .'</td>';
			echo '<td>'.$result_vin.'</td>';
			echo '<td>'.$result_responsedate.'</td>';
			echo '<td>'.$result_noofapi.'</td>';
			echo '<td>'.$result_15consecutingmissing.'</td>';
			echo '<td>'.$result_maxmissingduration.'</td>';
			echo '<td>'.$result_nooferrors.'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
	elseif(isset($_GET['data']) && $_GET['data'] == 'location'){
		$vin		=	null;
		$type		=	null;
		$fromdate	=	null;
		$todate		=	null;
		
		if(isset($_GET['vin']))		{	$vin		=	$_GET['vin'];		}
		if(isset($_GET['type']))	{	$type		=	$_GET['type'];		}
		if(isset($_GET['fromdate'])){	$fromdate	=	date('Y-m-d 00:00:00',strtotime($_GET['fromdate']));	}
		if(isset($_GET['todate']))	{	$todate		=	date('Y-m-d 23:59:59',strtotime($_GET['todate']));		}
		
		if($vin != 'All' ){
			$vin_filter	=	" && vin = '$vin'";
		}
		else{
			$vin_filter	=	null;
		}
		
		$gap_frequency_count		=	array();
		for($i=1;$i<=10000;$i++){
			$gap_frequency_count[]	=	15*$i;
		}
		$gap_frequency_count	=	implode("','",$gap_frequency_count);
		
		if($type == 'Cumulative'){
			$query	=	"SELECT vin, COUNT(id) as NoOfAPI, '".$_GET['fromdate']." to ".$_GET['todate']."' as ResponseDate,  SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN latitude<0  THEN 1 WHEN longitude<0 THEN 1 WHEN latitude_direction!='N' THEN 1 WHEN longitude_direction!='E' THEN 1 WHEN gps_validity_flag NOT IN ('TRUE', 'FALSE') THEN 1 WHEN gps_speed<0 OR gps_speed>100 THEN 1 ELSE 0 END) AS NoOfErrors from data_table_2 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin";
		}
		elseif($type == 'Daily'){
			$query	=	"SELECT vin, date(response_timestamp) as ResponseDate, COUNT(id) as NoOfAPI, SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN latitude<0  THEN 1 WHEN longitude<0 THEN 1 WHEN latitude_direction!='N' THEN 1 WHEN longitude_direction!='E' THEN 1 WHEN gps_validity_flag NOT IN ('TRUE', 'FALSE') THEN 1 WHEN gps_speed<0 OR gps_speed>100 THEN 1 ELSE 0 END) AS NoOfErrors from data_table_2 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin, date(response_timestamp)";
		}
		
	//	echo $query;
		echo <<<_END
		<h2>Location</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>	#							</th>
					<th>	VIN							</th>
					<th>	Period						</th>
					<th>	Total API					</th>
					<th>	Consecutive 15 Missing Data	</th>
					<th>	Max Data Missing Duration	</th>
					<th>	# of Errors					</th>
				</tr>
			</thead>
			<tbody>
_END;
		$exec	=	mysqli_query($db_connect, $query);
		$Sl		=	0;
		while($result	=	mysqli_fetch_array($exec)){
			$result_vin						=	$result['vin'];
			$result_noofapi					=	$result['NoOfAPI'];
			$result_15consecutingmissing	=	$result['15TotalConsecutiveMissing'];
			$result_maxmissingduration		=	$result['MaxMissingDuration'];
			$result_responsedate			=	$result['ResponseDate'];
			$result_nooferrors				=	$result['NoOfErrors'];
			$Sl								=	$Sl+1;
		//	echo $result_vin."|".$result_responsedate."|".$result_noofapi."|".$result_15consecutingmissing."|".$result_maxmissingduration."|".$result_nooferrors."</br>";
			echo '<tr>';
			echo '<td>'. $Sl .'</td>';
			echo '<td>'.$result_vin.'</td>';
			echo '<td>'.$result_responsedate.'</td>';
			echo '<td>'.$result_noofapi.'</td>';
			echo '<td>'.$result_15consecutingmissing.'</td>';
			echo '<td>'.$result_maxmissingduration.'</td>';
			echo '<td>'.$result_nooferrors.'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
	elseif(isset($_GET['data']) && $_GET['data'] == 'cluster'){
		$vin		=	null;
		$type		=	null;
		$fromdate	=	null;
		$todate		=	null;
		
		if(isset($_GET['vin']))		{	$vin		=	$_GET['vin'];		}
		if(isset($_GET['type']))	{	$type		=	$_GET['type'];		}
		if(isset($_GET['fromdate'])){	$fromdate	=	date('Y-m-d 00:00:00',strtotime($_GET['fromdate']));	}
		if(isset($_GET['todate']))	{	$todate		=	date('Y-m-d 23:59:59',strtotime($_GET['todate']));		}
		
		if($vin != 'All' ){
			$vin_filter	=	" && vin = '$vin'";
		}
		else{
			$vin_filter	=	null;
		}
		
		$gap_frequency_count		=	array();
		for($i=1;$i<=10000;$i++){
			$gap_frequency_count[]	=	15*$i;
		}
		$gap_frequency_count	=	implode("','",$gap_frequency_count);
		
		if($type == 'Cumulative'){
			$query	=	"SELECT vin, COUNT(id) as NoOfAPI, '".$_GET['fromdate']." to ".$_GET['todate']."' as ResponseDate,  SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN ttc<0 OR ttc>1440  THEN 1 WHEN door_open_indicator NOT IN ('0','1') THEN 1 WHEN parking_brake NOT IN ('0','1') THEN 1 WHEN gear_position NOT IN ('Forward', 'Reverse', 'Neutral', 'Boost') THEN 1 ELSE 0 END) AS NoOfErrors from data_table_3 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin";
		}
		elseif($type == 'Daily'){
			$query	=	"SELECT vin, date(response_timestamp) as ResponseDate, COUNT(id) as NoOfAPI, SUM(CASE WHEN zero_gap_frequency in ('$gap_frequency_count') THEN 1 ELSE 0 END) as 15TotalConsecutiveMissing, MAX(zero_gap_frequency)*2 as MaxMissingDuration, SUM(CASE WHEN ttc<0 OR ttc>1440  THEN 1 WHEN door_open_indicator NOT IN ('0','1') THEN 1 WHEN parking_brake NOT IN ('0','1') THEN 1 WHEN gear_position NOT IN ('Forward', 'Reverse', 'Neutral', 'Boost') THEN 1 ELSE 0 END) AS NoOfErrors from data_table_3 WHERE response_timestamp>='$fromdate' && response_timestamp<='$todate' $vin_filter GROUP BY vin, date(response_timestamp)";
		}
		
	//	echo $query;
		echo <<<_END
		<h2>Cluster</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>	#							</th>
					<th>	VIN							</th>
					<th>	Period						</th>
					<th>	Total API					</th>
					<th>	Consecutive 15 Missing Data	</th>
					<th>	Max Data Missing Duration	</th>
					<th>	# of Errors					</th>
				</tr>
			</thead>
			<tbody>
_END;
		$exec	=	mysqli_query($db_connect, $query);
		$Sl		=	0;
		while($result	=	mysqli_fetch_array($exec)){
			$result_vin						=	$result['vin'];
			$result_noofapi					=	$result['NoOfAPI'];
			$result_15consecutingmissing	=	$result['15TotalConsecutiveMissing'];
			$result_maxmissingduration		=	$result['MaxMissingDuration'];
			$result_responsedate			=	$result['ResponseDate'];
			$result_nooferrors				=	$result['NoOfErrors'];
			$Sl								=	$Sl+1;
		//	echo $result_vin."|".$result_responsedate."|".$result_noofapi."|".$result_15consecutingmissing."|".$result_maxmissingduration."|".$result_nooferrors."</br>";
			echo '<tr>';
			echo '<td>'. $Sl .'</td>';
			echo '<td>'.$result_vin.'</td>';
			echo '<td>'.$result_responsedate.'</td>';
			echo '<td>'.$result_noofapi.'</td>';
			echo '<td>'.$result_15consecutingmissing.'</td>';
			echo '<td>'.$result_maxmissingduration.'</td>';
			echo '<td>'.$result_nooferrors.'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

?>