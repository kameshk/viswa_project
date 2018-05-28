<?php
	require_once __DIR__ . '/config/config.php';
	require_once __DIR__ . '/function.php';
	
	/** PHPExcel_IOFactory */
	include './lib/PHPExcel_1.8.0_doc/Classes/PHPExcel/IOFactory.php';
	
	//Update files to DB Script
	$files = scandir($data_directory_cluster_path);
	foreach($files as $filename){
		$file_explode	=	explode('.',$filename);
		$file_extn		=	end($file_explode);
		if($file_extn == 'csv'){
		//	echo $filename."</br>";
			$inputFileName		=	$data_directory_cluster_path."/".$filename;
			$inputFileType		=	$file_extn;
		//	echo $inputFileName."</br>";
			
			//  Read your Excel workbook
			try {
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}

			//  Get worksheet dimensions
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();

			//  Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++){ 
				/*
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				*/
				$col				=	'A';
				$vin				=	$sheet->getCell($col++.$row)->getValue();
				$ttc				=	$sheet->getCell($col++.$row)->getValue();
				$door_open_indicator=	$sheet->getCell($col++.$row)->getValue();
				$parking_brake		=	$sheet->getCell($col++.$row)->getValue();
				$gear_position		=	$sheet->getCell($col++.$row)->getValue();
				$data_timestamp		=	date('Y-m-d H:i:s',strtotime($sheet->getCell($col++.$row)->getFormattedValue()));
				$response_timestamp	=	date('Y-m-d H:i:s',strtotime($sheet->getCell($col++.$row)->getFormattedValue()));
				$key_column			=	$vin.'|'.$response_timestamp;

				//	echo $vin."||".$vehicle_mode."||".$odometer."||".$speed."||".$soc."||".$dte."||".$ac_status."||".$data_timestamp."||".$response_timestamp."</br>";

				//Get the Last Entry Time
				$getLastEntryQuery		=	"SELECT data_timestamp, zero_gap_frequency FROM data_table_3 WHERE vin='$vin' && response_timestamp<='$response_timestamp' order by response_timestamp desc limit 0,1";
			//	echo $getLastEntryQuery."</br>";
				$getLastEntryExec		=	mysqli_query($db_connect, $getLastEntryQuery);
				if(!$getLastEntryExec) die('Unable to fetch the last entry for the VIN:'.$vin.'. Due to '.mysqli_error($db_connect));
				$getLastEntryRes		=	mysqli_fetch_assoc($getLastEntryExec);
				$LastDataTimestamp		=	$getLastEntryRes['data_timestamp'];
				$LastZeroGapFrequency	=	$getLastEntryRes['zero_gap_frequency'];
				
				$datetime1	= new DateTime($data_timestamp);
				$datetime2	= new DateTime($LastDataTimestamp);
				$interval	= date_diff($datetime1, $datetime2);
				$time_gap	= $interval->format('%H:%i:%s');
				$time_gap	=	getHMS($LastDataTimestamp, $data_timestamp);
			//	echo $data_timestamp."||".$LastDataTimestamp."||".$LastZeroGapFrequency."||".$time_gap."</br>";
				if($time_gap == "00:00:00"){
					$zero_gap_frequency	=	$LastZeroGapFrequency+1;
				}
				else{
					$zero_gap_frequency	=	0;
				}
				
				
				
			//	echo $vin."||".$vehicle_mode."||".$odometer."||".$speed."||".$soc."||".$dte."||".$ac_status."||".$data_timestamp."||".$response_timestamp."||".$time_gap."||".$zero_gap_frequency."</br>";
				
				$InsertQuery	=	"INSERT INTO data_table_3(key_column, vin, ttc, door_open_indicator, parking_brake, gear_position, data_timestamp, response_timestamp, time_gap, zero_gap_frequency, filename) VALUES('$key_column', '$vin', '$ttc', '$door_open_indicator', '$parking_brake', '$gear_position', '$data_timestamp', '$response_timestamp', '$time_gap', '$zero_gap_frequency', '$inputFileName') ON DUPLICATE KEY UPDATE vin='$vin', ttc='$ttc', door_open_indicator='$door_open_indicator', parking_brake='$parking_brake', gear_position='$gear_position', data_timestamp='$data_timestamp', time_gap='$time_gap', zero_gap_frequency='$zero_gap_frequency', filename='$inputFileName'";
				$InsertExec		=	mysqli_query($db_connect, $InsertQuery);
				if(!$InsertExec) die('Unable to insert the entry for '.$vin.'. Due to '.mysqli_error($db_connect));
			}
			
			shell_exec("mv '$inputFileName' '/processed/cluster/$filename'");
		//	exec("mv '$inputFileName' '/processed/$filename'");
		}
	}
	
	echo 'Execution Completed';
?>