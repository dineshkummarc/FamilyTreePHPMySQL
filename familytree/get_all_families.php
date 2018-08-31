<?php
require_once('config.php');

$DBH;
try {
    $DBH = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    $STH = $DBH->prepare("SELECT `idfamily`, `familyname` FROM `tblfamily` WHERE `idfamily` != 1 ORDER BY `familyname`;");
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $data = array();
    while($row = $STH->fetch()) {
        array_push($data,["idfamily"=>$row->idfamily,
        "familyname"=>$row->familyname]);
    }
	$DBH = null;
    header('Content-Type: application/json');
    echo json_encode($data);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$DBH = null;