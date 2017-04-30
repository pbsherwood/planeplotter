<?php 
	$interval = $_POST['interval'];

	$con = mysqli_connect("SERVER","USERNAME","PASSWORD");
	if (!$con)
	{
		die('Could not connect: ' . mysqli_error());
	}
	mysqli_select_db($con, "DATABASE");
	
	$result = mysqli_query($con, "select latitude, longitude, heading from data where last_update_time between UNIX_TIMESTAMP(now())-" . $interval . " AND UNIX_TIMESTAMP(now())");
	
	$coords = Array();
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$lat = round((float)$row["latitude"], 3);
		$long = round((float)$row["longitude"], 3);
		$heading = $row["heading"];
		$temparray = array($lat, $long, $heading);
		array_push($coords, $temparray);
	}
	
	echo json_encode($coords);
	
?>