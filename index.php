<?php
	//UI
	require_once __DIR__ . '/config/config.php';
	require_once __DIR__ . '/function.php';
	
	//get the Vin List
	$vin_array	=	getVinListArray();
	
	//get the Type list
	$type_array	=	getTypeListArray();
?>
<!DOCTYPE HTML>
<html>
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
		
		<title><?php echo $ProjectName; ?></title>
		
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script>
		$( function() {
			$( "#fromdate" ).datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:"dd-M-yy"
			});
			$( "#todate" ).datepicker({
				changeMonth:true,
				changeYear:true,
				dateFormat:"dd-M-yy"
			});
		  } );
		</script>
		
		<script src="./lib/js/jquery-min-3.3.1.js"></script>
		
		<link rel="stylesheet" href="./lib/bootstrap-4.1.1/dist/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="./lib/bootstrap-4.1.1/dist/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
		
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-4"></div>
				<div class="col-sm-4 text-center"><?php echo $ProjectName; ?></div>
				<div class="col-sm-4"></div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-1">Vin:</div>
				<div class="col-sm-2">
					<select name="vin" id="vin">
					<?php
						foreach($vin_array as $vin_name){
							echo '<option value="'.$vin_name.'">'.$vin_name.'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-sm-1">Type</div>
				<div class="col-sm-1">
					<select name="type" id="type">
					<?php
						foreach($type_array as $type_name){
							echo '<option value="'.$type_name.'">'.$type_name.'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-sm-1">From Date</div>
				<div class="col-sm-2"><input type="text" name="fromdate" id="fromdate"/></div>
				<div class="col-sm-1">To Date</div>
				<div class="col-sm-2"><input type="text" name="todate" id="todate"/></div>
				<div class="col-sm-1"><div class="btn btn-block btn-primary" onclick="getData();">View</div></div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12" id="status_report">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12" id="location_report">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12" id="cluster_report">
				</div>
			</div>
		</div>
	</body>
<html>
<script>
	$(document).ready(function(){
		$("#vin").change(function(){
			if($("#vin").val() == 'All'){
				$("#type").html('<option value="Cumulative">Cumulative</option>');
			}
			else{
				$("#type").html('<option value="Cumulative">Cumulative</option><option value="Daily">Daily</option>');
			}
		});
	});
	
	function getData(){
		
		var vin		=	$("#vin").val();
		var type	=	$("#type").val();
		var fromdate=	$("#fromdate").val();
		var todate	=	$("#todate").val();
		
		if(vin == '')		{alert('VIN no. is missing'); return false;}
		else if(type == '')	{alert('Type is missing'); return false;}
		else if(fromdate == '')	{alert('From date is missing'); return false;}
		else if(todate == '')	{alert('To date is missing'); return false;}
		else{
		//	alert(vin);
		//	alert(type);
		//	alert(fromdate);
		//	alert(todate);
		//	alert('get.php?data=status&vin=' + encodeURIComponent(vin) + '&type=' + encodeURIComponent(type) + '&fromdate=' + encodeURIComponent(fromdate) + '&todate=' + encodeURIComponent(todate));
			$.get('get.php?data=status&vin=' + encodeURIComponent(vin) + '&type=' + encodeURIComponent(type) + '&fromdate=' + encodeURIComponent(fromdate) + '&todate=' + encodeURIComponent(todate) , function(data){
				$("#status_report").html(data);
			});
			$.get('get.php?data=location&vin=' + encodeURIComponent(vin) + '&type=' + encodeURIComponent(type) + '&fromdate=' + encodeURIComponent(fromdate) + '&todate=' + encodeURIComponent(todate) , function(data){
				$("#location_report").html(data);
			});
			$.get('get.php?data=cluster&vin=' + encodeURIComponent(vin) + '&type=' + encodeURIComponent(type) + '&fromdate=' + encodeURIComponent(fromdate) + '&todate=' + encodeURIComponent(todate) , function(data){
				$("#cluster_report").html(data);
			});
		}
	}
</script>