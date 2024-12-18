<?php

// hier wird eine Verbindung zur Datenbank hergestellt

$sname = "web06.iis.uni-bamberg.de:3307";
$uname = "wip21_g2";
$password = "HNEYmSG62";
$db_name = "wip21_g2";

$conn = mysqli_connect($sname, $uname, $password, $db_name);

if (!$conn) {
	echo "Connection Failed!";
	exit();
}
