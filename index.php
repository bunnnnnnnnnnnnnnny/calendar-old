<html>

<?php

$passwordhash = getenv('CAL_PASS');

$imagesDir = '../images/';
$images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$randomImage = $images[array_rand($images)]; // See comments

$imageshere = glob('*.{jpg,jpeg,png,gif}', GLOB_BRACE);

echo '
<link rel="stylesheet" type="text/css" href="style.css" />
';

if((md5($_POST["password"]) != $passwordhash) && ( $_COOKIE["password"] != $passwordhash)){
	echo '
	</div class="passwordentrybox">
		<form class="" action="" method="post" autocomplete="off">
			<input type="password" name="password">
			<button type="submit" name="submit">Enter</button>
		</form>
	</div>';
}

echo '</br>';

echo '<body>';

echo "<img src='" . $randomImage . "' id='centerimg'></body>";

$monthyear = date("Y-m");

if(isset($_COOKIE["monthyear"])){
	#$month = 
	#$monthend = 
	$monthyear = $_COOKIE["monthyear"];
}else{
	$monthyear = date("Y-m");
}

$realmonthyear = date("Y-m");

$firstweekday = date('w', strtotime($monthyear . "-1"));
if ($firstweekday == 0){
	$firstweekday = 7;
}
$day = 2 - $firstweekday;
$month = date("F", strtotime($monthyear));
$monthend = date("t", strtotime($monthyear));
$year =  date("Y");
#$test_date = "2040-11-23";
#echo date("Y-m-t", strtotime("2040-11"));
#$monthyear = date("Y-m");

$maxweek = 4;

#if(( $firstweekday > 5 ) && ( $monthend > 30 ) ){
#	$maxweek++;
#}

if( $monthend + $firstweekday - 1 > 35 ){
	$maxweek = 6;
} elseif( $monthend + $firstweekday - 1 > 28 ){
	$maxweek = 5;
}

$user = "root";
$password = "rusted";
$database = "bunnny";
$servername = "localhost";
$conn = mysqli_connect($servername, $user, $password, $database);
$daystable = mysqli_query($conn, "select * from calendar where date like '$monthyear%';");
$eventstable = mysqli_query($conn, "select * from events where month = " . date("n", strtotime($monthyear . "-01")) . ";");
$days = array(0=>"");
$events = array(0=>"");
#echo "<pre>". print_r($thing) . "</pre>";
foreach($daystable as $row4){
	#echo date("d", strtotime($row4["date"]));
	#echo $row4["text"];
	#echo "</br>";
	$days[date("j", strtotime($row4["date"]))] = $row4["text"];
}

foreach($eventstable as $row5){
	#echo date("d", strtotime($row4["date"]));
	#echo $row4["text"];
	#echo "</br>";
	$events[date("j", strtotime($year . "-" . $row5["month"] . "-" .  $row5["day"]))] = $row5["text"] . "</br>" . $events[date("j", strtotime($year . "-" . $row5["month"] . "-" .  $row5["day"]))];
}

#echo $row4['2022-11-29'];
#echo $days[29];
#echo "<pre>". print_r($days) . "</pre>";

function printday($currday=null, $monthend=31, $days=array(0=>""), $monthyear="date(\"Y-m\")", $events=array(0=>"")){
	$tdid = "";
	$caleventlines = substr_count($events[$currday], "</br>") * 10;
	#$caleventlines = $caleventlines * 10;
	if(date("Y-m-j", strtotime($monthyear . "-" . $currday)) == date("Y-m-j")){
		$tdid = " id='currentday'";
	}
	$properdate = $monthyear . '-' . $currday;
	if(($currday > 0) && ($currday <= $monthend)){
		echo "<td" . $tdid . "><div class='date'" . $tdid . ">" . ($currday) .
		"</div><textarea spellcheck='false' form='calendar' name='". $properdate . "' id='". $properdate . "'>" . $days[$currday] ."</textarea>" . 
		'<div class="calevent" style="margin-top: -' . $caleventlines . 'px;">' . $events[$currday] . '</div>' . "</td>";
	}
	else{ # if(($currday <= $monthend))
		echo "<td class='empty'></td>";	
		#echo "<td><div class='date'>" . ($currday) . "</div><textarea spellcheck='false'></textarea></td>";
	}
}

function printweek($day=0, $monthend=0, $days=0, $monthyear='', $events=0){
	$end = $day+6;
	while ($day <= $end){
		printday($day, $monthend, $days, $monthyear, $events);
		$day++;
	}
}

function printmonthlyevents($events, $monthend, $month){
	$day = 0;
	echo "<table id='events'>";
	echo "<tr><td id='eventstitle'>Events this month:</td></tr>";
	while($day <= $monthend){
		if(isset($events[$day])){
			if($day != 0){
				echo "<tr><td id='events'>";
				echo $month . " " . $day;
				if (in_array($day, array(1, 21, 31))){
					echo "st";
				}elseif (in_array($day, array(3, 23))){
					echo "rd";
				}else{
					echo "th";
				}
				echo ": " . $events[$day] . "</br>";
				echo "</td></tr>";
			}
		}
		$day++;
		}
	echo "</table>";
}

function navbutton($goto='next', $monthyear=''){
	if($goto == 'next'){
		$modifier = "+1 month";
	}else{
		$modifier = "-1 month";
	}
	$goto = $goto . 'month';
	$monthyear = date("Y-m", strtotime($modifier, strtotime($monthyear)));
	return "
	<form id='" . $goto . "' name='" . $goto . "' action='calendarsubmission.php' method='get'></form>
	<button type='submit' class='button' form='" . $goto . "' name='month' value='" . $monthyear . "'>
		<img src='" . $goto . ".svg' width=32px></img>&NoBreak; 
	</button>
	";
}

if(md5($_POST["password"]) == $passwordhash) {
	setcookie("password", md5($_POST["password"]), time() + (86400 * 30), "/");
}

if((md5($_POST["password"]) == $passwordhash) || ( $_COOKIE["password"] == $passwordhash)){
#	echo '<div id="imagelist">';
#	foreach($imageshere as $i){
#		echo "<img src='" . $i . "' id='ree'></img>";
#	}
#	echo "</div>";
echo "<div class='maincontainer'>";
echo "<div class=calendarbody>

<div id='toolbarmiddle'>
" . navbutton("prev", $monthyear);
#echo "<button name='monthname'>" . $month . date(" Y", strtotime($monthyear)) . "</button>"

echo "<form id='current' name='current' action='calendarsubmission.php' method='get'></form>
      <button type='submit' class='button' form='current' name='month' value='" . $realmonthyear . "'>"
	  . $month . date(" Y", strtotime($monthyear)) . "</button>";

echo navbutton("next", $monthyear) . "

<button type='submit' class='button' form='calendar' name='calendar'>Update
</button>
</div>

<form id='calendar' name='calendar' action='calendarsubmission.php' method='post'>
</form>



<table>
	<tr>
		<th>Monday</th>
		<th>Tuesday</th>
		<th>Wednesday</th>
		<th>Thursday</th>
		<th>Friday</th>
		<th>Saturday</th>
		<th>Sunday</th>
	</tr>"; # clean this up
		$week = 1;
		while ($week <= $maxweek) {
			echo "<tr>";
			printweek($day, $monthend, $days, $monthyear, $events);
			$day = $day + 7;
			$week++;
			echo "</tr>";
		}
echo "</table></div>";
echo "<div class='otherbody'>";
printmonthlyevents($events, $monthend, $month);
echo "</div>"; # otherbody
echo "</div>"; # bigcontainer
}

#echo htmlentities($_GET["month"]);

#echo date("Y-m-j");

?>





</html>
