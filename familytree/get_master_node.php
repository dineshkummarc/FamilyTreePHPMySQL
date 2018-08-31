<?php
$idfamily = 0;
if(isset($_GET["familyid"]) && $_GET["familyid"] != null && $_GET["familyid"] > 0){ 
	$idfamily = $_GET["familyid"];
}else{
    $idfamily = 2; 
}
$idperson = 0;
if(isset($_GET["idperson"]) && $_GET["idperson"] != null && $_GET["idperson"] > 0){ 
	$idperson = $_GET["idperson"];
}

$redirectcount = 0;
if(isset($_GET["redirectcount"]) && $_GET["redirectcount"] != null && $_GET["redirectcount"] > 0){ 
	$redirectcount = $_GET["redirectcount"];
}

require_once('config.php');

$DBH;
try {
    $DBH = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    $STH = $DBH->prepare("SELECT `tpm`.`personname`, `tpm`.`idperson`, `tpm`.`nickname`,`tpm`.`dateofbirth`,`tpm`.`dateofdeath`, `familyname`, `gender`, `profilepic`
    ,CASE WHEN (SELECT COUNT(*) FROM `tblp_to_p_link` WHERE `idperson_from` = `tpm`.`idperson`) > 0  THEN true ELSE false END AS `haschild`
    ,(SELECT `tp`.`personname` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner`
    ,(SELECT `tp`.`nickname` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_nickname`
    ,(SELECT `tp`.`dateofbirth` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_dateofbirth`
	,(SELECT `tp`.`dateofdeath` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_dateofdeath`
	,(SELECT `fm`.`familyname` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
	  INNER JOIN `tblfamily` AS `fm` ON `fm`.`idfamily` = `tp`.`idfamily`
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_familyname`
	,(SELECT `tp`.`gender` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_gender`
	,(SELECT `tp`.`profilepic` FROM `tblperson` AS `tp`
      INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tpm`.`idperson`
      LIMIT 1
    ) as `partner_profilepic`
    FROM `tblperson` AS `tpm`
	INNER JOIN `tblfamily` ON `tblfamily`.`idfamily` = `tpm`.`idfamily`
	WHERE `tpm`.`idperson` NOT IN ( 
	SELECT CASE WHEN :idperson = 0 THEN `idperson_to` ELSE 0 END FROM `tblp_to_p_link` WHERE `idrelation` = 1)
    AND `tblfamily`.`idfamily` = :idfamily
	AND `tblfamily`.`idfamily` != 1
	AND `tpm`.`idperson` = CASE WHEN :idperson = 0 THEN `tpm`.`idperson` ELSE :idperson END
	ORDER BY `tpm`.`dateofbirth` ASC, `tpm`.`idperson` ASC LIMIT 1;");
	
	$STH->bindParam("idfamily", $idfamily);
	$STH->bindParam("idperson", $idperson);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $data = array();
    //$data = "{";
    while($row = $STH->fetch()) {
        $d = new DateTime($row->dateofbirth);
        $dp = new DateTime($row->partner_dateofbirth);
		$dpd = new DateTime($row->partner_dateofdeath);
        $age = 0;
        if($row->dateofbirth != null){
            //$tz  = new DateTimeZone('Europe/Brussels');
            $age = $d->diff(
                //new DateTime('now', $tz)
                new DateTime()
            )->y;            
        }
        $agep = 0;
        if($row->dateofbirth != null){
            //$tz  = new DateTimeZone('Europe/Brussels');
            $agep = $dp->diff(
                //new DateTime('now', $tz)
                new DateTime()
            )->y;            
        }
        array_push($data,["id"=>$row->idperson,"name"=>$row->personname,
        "familyname"=>$row->familyname,
        "gender"=>$row->gender,
        "profilepic"=>$row->profilepic,
        "nickname"=>$row->nickname,
        "dob"=> ($row->dateofbirth == null ? $row->dateofbirth : $d->format('d-M-Y')),
        "dod"=> ($row->dateofdeath == null ? $row->dateofdeath : $d->format('d-M-Y')),
        "age"=> ($row->dateofbirth == null ? null : $age),
        "haschild"=>$row->haschild,
        "partner"=>$row->partner,
        "partner_nickname"=>$row->partner_nickname,
        "partner_dateofbirth"=>($row->partner_dateofbirth == null ? $row->partner_dateofbirth : $dp->format('d-M-Y')),
		"partner_dateofdeath"=> ($row->partner_dateofdeath == null ? $row->partner_dateofdeath : $dpd->format('d-M-Y')),
        "partner_age"=> ($row->partner_dateofbirth == null ? null : $agep),
        "partner_familyname"=> $row->partner_familyname,
        "partner_gender"=> $row->partner_gender,
        "partner_profilepic"=> $row->partner_profilepic,
        "children"=>null]);
        //$data .= "'name':'" .$row->personname ."',";
		
		if($row->gender == 'Female'){
			$DBHP = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$DBHP->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$STHP = $DBHP->prepare("SELECT `idperson`, `idfamily` FROM `tblperson` 
			WHERE `idperson` = 
			(SELECT `idperson_from` FROM `tblp_to_p_link` WHERE `idperson_to` = :idperson_to) LIMIT 1;");
			$STHP->bindParam("idperson_to", $row->idperson);
			$STHP->execute();
			$STHP->setFetchMode(PDO::FETCH_OBJ);
			while($rowp = $STHP->fetch()) {
				//print_r($rowp)  ;
				if($redirectcount == 0){
					header("Location: get_master_node.php?familyid=". $rowp->idfamily ."&idperson=". $rowp->idperson ."&redirectcount=1"); 
				}
			}
		}
    }
    //$data = substr($data, 0, -1);
    //$data .= "}";
	$DBH = null;
    header('Content-Type: application/json');
    echo json_encode($data);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$DBH = null;