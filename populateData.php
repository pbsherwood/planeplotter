<?php 
	$interval = $_POST['interval'];

	$con = mysqli_connect("SERVER","USERNAME","PASSWORD");
	if (!$con)
	{
		die('Could not connect: ' . mysqli_error());
	}
	mysqli_select_db($con, "DATABASE");
	
	$result = mysqli_query($con, "select ICAO, callsign, altitude, ground_speed, heading, latitude, longitude, vertical_rate, ssr, alert, emergency, spi, is_on_ground, from_unixtime(last_update_time) as last_update_time, from_unixtime(last_location_update_time) as last_location_update_time from data where last_update_time between UNIX_TIMESTAMP(now())-" . $interval . " AND UNIX_TIMESTAMP(now())");
	
	$info = Array();
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$ICAO = $row["ICAO"];
		$callsign = $row["callsign"];
		$altitude = $row["altitude"];
		$ground_speed = $row["ground_speed"];
		$heading = $row["heading"];
		$latitude = round((float)$row["latitude"], 3);
		$longitude = round((float)$row["longitude"], 3);
		$vertical_rate = $row["vertical_rate"];
		$ssr = $row["ssr"];
		$alert = $row["alert"];
		$emergency = $row["emergency"];
		$spi = $row["spi"];
		$is_on_ground = $row["is_on_ground"];
		$last_update_time = $row["last_update_time"];
		$last_location_update_time = $row["last_location_update_time"];
		
		$temparray = array($ICAO, $callsign, $altitude, $ground_speed, $heading, $latitude, $longitude, $vertical_rate, $ssr, $alert, $emergency, $spi, $is_on_ground, $last_update_time, $last_location_update_time);
		array_push($info, $temparray);
	}
	
	echo json_encode($info);
	
?>