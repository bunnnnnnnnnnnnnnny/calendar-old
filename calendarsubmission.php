<HTML>
<?php

$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$servername = getenv('DB_HOST');
$conn = mysqli_connect($servername, $user, $password, $database);


########## this whole bit is unnecessary if i just move this to index.php
$monthyear = date("Y-m", strtotime(array_keys($_POST)[1])); #  array_keys($_POST)[1]; #
$thing = mysqli_query($conn, "select * from calendar where date like '$monthyear%';");
$days = array(0=>"");
foreach($thing as $row){
 $days[date("Y-m-j", strtotime($row["date"]))] = $row["text"];
}

foreach ($_POST as $key => $value) {
	if (($days[$key] != $value) && ($value != "")) { 
		mysqli_query($conn, "insert into calendar (date, text) values ('$key', '" . htmlentities(str_replace("'", "\'", $value)) . "')
		on duplicate key update text='" . htmlentities(str_replace("'", "\'", $value)) . "';");
	} elseif (($days[$key] != $value) && $value == "") {
		mysqli_query($conn, "delete from calendar where date = '$key';");
	}
}

if(isset($_GET["month"])){
	$a = htmlentities($_GET['month']);
	setcookie("monthyear", $a, time() + (86400 * 30), "/");
}

mysqli_close($conn);
header("Location: .");

echo '<pre>'; print_r($_POST); echo '</pre>';
echo '<pre>'; print_r($days); echo '</pre>';
echo $monthyear;
echo htmlspecialchars($_POST[1][0]);
echo array_keys($_POST)[1];
exit();

?>
</HTML>
