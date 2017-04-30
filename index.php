<head>
	<script src="jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
</head>
<body onload="onLoad()" style="background: #000;">

<div id="map_canvas" style="width: 100%; height: 60%;"></div>
<select id="interval" onchange="onLoad()"><option value="60">1 min</option><option value="120">2 mins</option><option value="300">5 mins</option><option value="600">10 mins</option><option value="1800">30 mins</option><option value="3600">1 hour</option><option value="86400">1 day</option><option value="604800">1 week</option><option value="2419200">1 month</option><option value="50000000">ALL</option></select>
<div id="data" style="width: 100%; height: 40%; background-color: black; overflow:scroll;"></div>

<!-- --------------------------------------------------------------------------------------------------------- -->

<script>

var flightCoordinates = new Array();
var headings = new Array();

var myLatlng = new google.maps.LatLng(43.25, -79.87);

var myOptions = {
  zoom: 14,
  center: myLatlng,
  mapTypeId: google.maps.MapTypeId.ROADMAP
}
map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

var bounds = new google.maps.LatLngBounds ();
bounds.extend (new google.maps.LatLng(42.8, -81));
bounds.extend (new google.maps.LatLng(44, -79));
map.fitBounds (bounds);

	function onLoad()
	{
		var picked = $('#interval').find(":selected").val();
	
		data_grabber_1 = "populateMap.php";
		data_grabber_2 = "populateData.php";
		data_grabber_params = "interval=" + picked;
		
		$.ajax({
			type : "POST",
			url : data_grabber_1,
			data: data_grabber_params,
			success: function(data)
			{
				var coords = JSON.parse(data);
				flightCoordinates.length = 0;
				headings.length = 0;
				for (var arrayIndex in coords)
				{
					var latitude = coords[arrayIndex][0];
					var longitude = coords[arrayIndex][1];
					var heading = coords[arrayIndex][2];
					
					if (latitude != 0 && longitude != 0)
					{
						flightCoordinates.push(new google.maps.LatLng(latitude, longitude));
						headings.push(Number(heading));
					}
					else
					{
						flightCoordinates.push(new google.maps.LatLng(0, 0));
						headings.push(Number(0));
					}
				}
				
				updateMap();
			},
			error: function(request,error)
			{
			},
			timeout: 60000
		});

		$.ajax({
			type : "POST",
			url : data_grabber_2,
			data: data_grabber_params,
			success: function(data)
			{	
				$("#data").html("<br>");
				
				var $table = $("<table id=\"dataTable\" border=\"1\" style=\"color:#fff\">");
				$table.append("<thead>").children("thead")
				.append("<tr />").children("tr").append("<th>ICAO</th><th>Callsign</th><th>Altitude</th><th>Ground Speed</th><th>Heading</th><th>Latitude</th><th>Longitude</th><th>Vertical Rate</th><th>SSR</th><th>Alert</th><th>Emergency</th><th>SPI</th><th>Is On Ground</th><th>Last Update Time</th><th>Last Location Update Time</th>");
				var $tbody = $table.append("<tbody />").children("tbody");
				
				
				var info = JSON.parse(data);
				
				for (var arrayIndex in info)
				{
					var ICAO = info[arrayIndex][0];
					var callsign = info[arrayIndex][1];
					var altitude = info[arrayIndex][2];
					var ground_speed = info[arrayIndex][3];
					var heading = info[arrayIndex][4];
					var latitude = info[arrayIndex][5];
					var longitude = info[arrayIndex][6];
					var vertical_rate = info[arrayIndex][7];
					var ssr = info[arrayIndex][8];
					var alert = info[arrayIndex][9];
					var emergency = info[arrayIndex][10];
					var spi = info[arrayIndex][11];
					var is_on_ground = info[arrayIndex][12];
					var last_update_time = info[arrayIndex][13];
					var last_location_update_time = info[arrayIndex][14];
					
					// add row
					$tbody.append("<tr />").children("tr:last")
					.append("<td>" + ICAO + "</td>")
					.append("<td>" + callsign + "</td>")
					.append("<td>" + altitude + "</td>")
					.append("<td>" + ground_speed + "</td>")
					.append("<td>" + heading + "</td>")
					.append("<td>" + latitude + "</td>")
					.append("<td>" + longitude + "</td>")
					.append("<td>" + vertical_rate + "</td>")
					.append("<td>" + ssr + "</td>")
					.append("<td>" + alert + "</td>")
					.append("<td>" + emergency + "</td>")
					.append("<td>" + spi + "</td>")
					.append("<td>" + is_on_ground + "</td>")
					.append("<td>" + last_update_time + "</td>")
					.append("<td>" + last_location_update_time + "</td>");
				}
				
				$("#data").append($table);
				
			},
			error: function(request,error)
			{
			},
			timeout: 60000
		});

	}
</script>

<!-- --------------------------------- MAP STUFF --------------------------------- -->

<script type="text/javascript"> 

var map;
var markersArray = [];

function updateMap()
{
	if (typeof(flightCoordinates) != "undefined")
	{
		clearOverlays();
		for (var i=0; i < flightCoordinates.length; i++)
		{ 
			currentlocation = flightCoordinates[i];
			heading = headings[i];
			
			if (heading >= 348.75 || heading < 11.25)
				headingIMG = "images/plane-N.png";
			else if (heading >= 11.25 && heading < 33.75)
				headingIMG = "images/plane-NNE.png";
			else if (heading >= 33.75 && heading < 56.25)
				headingIMG = "images/plane-NE.png";
			else if (heading >= 56.25 && heading < 78.75)
				headingIMG = "images/plane-ENE.png";
			else if (heading >= 78.75 && heading < 101.25)
				headingIMG = "images/plane-E.png";
			else if (heading >= 101.25 && heading < 123.75)
				headingIMG = "images/plane-ESE.png";
			else if (heading >= 123.75 && heading < 146.25)
				headingIMG = "images/plane-SE.png";
			else if (heading >= 146.25 && heading < 168.75)
				headingIMG = "images/plane-SSE.png";
			else if (heading >= 168.75 && heading < 191.25)
				headingIMG = "images/plane-S.png";
			else if (heading >= 191.25 && heading < 213.75)
				headingIMG = "images/plane-SSW.png";
			else if (heading >= 213.75 && heading < 236.25)
				headingIMG = "images/plane-SW.png";
			else if (heading >= 236.25 && heading < 258.75)
				headingIMG = "images/plane-WSW.png";
			else if (heading >= 258.75 && heading < 281.25)
				headingIMG = "images/plane-W.png";
			else if (heading >= 281.25 && heading < 303.75)
				headingIMG = "images/plane-WNW.png";
			else if (heading >= 303.75 && heading < 326.25)
				headingIMG = "images/plane-NW.png";
			else if (heading >= 326.25 && heading < 348.75)
				headingIMG = "images/plane-NNW.png";
			
			marker = new google.maps.Marker({
                        position: currentlocation,
                        icon: headingIMG
                        });
            marker.setMap(map);
			
			markerFunctionCall(marker, i);
			
			markersArray.push(marker);
		}
	}
}


function clearOverlays() {
  for (var i = 0; i < markersArray.length; i++ ) {
    markersArray[i].setMap(null);
  }
  markersArray = [];
}

function markerFunctionCall(marker, index) {
	google.maps.event.addListener(marker, 'click', function() {
		highlight(index+1);
		alert(index+1);
	});
}

window.setInterval(function(){
	var picked = $('#interval').find(":selected").val();
	if (picked == "60")
	{
		onLoad();
	}
}, 5000);


function highlight(index){
 var table = document.getElementById('dataTable');
 for (var i=0;i < table.rows.length;i++){
  if (index == i)
  {
   if(!table.rows[i].hilite){
    unhighlight();
    table.rows[i].origColor=table.rows[i].style.backgroundColor;
    table.rows[i].style.backgroundColor='#FFFFFF';
	table.rows[i].style.color='#000000';
    table.rows[i].hilite = true;
   }
   else{
    table.rows[i].style.backgroundColor=table.rows[i].origColor;
	table.rows[i].style.color='#FFFFFF';
    table.rows[i].hilite = false;
   }
  }
 }
}

function unhighlight(index){
 var table = document.getElementById('dataTable');
 for (var i=0;i < table.rows.length;i++){
   var row = table.rows[i];
   row.style.backgroundColor=table.rows[i].origColor;
   	table.rows[i].style.color='#FFFFFF';
   row.hilite = false;
 }
}
</script>
</body>