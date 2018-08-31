<?php
$id = $_GET["id"];
if($id == null || $id <= 0){
    //header('Content-Type: application/json');
    echo json_encode("{}");
    return;
}

require_once('config.php');

$DBH;
try {
    $DBH = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $STH = $DBH->prepare("SELECT `tblperson`.`personname`,`tblperson`.`idperson`
    ,`tblperson`.`nickname`
    , `tblperson`.`dateofbirth`
    , `tblperson`.`dateofdeath`
	, `tblfamily`.`familyname`
	, `tblperson`.`gender`
	, `tblperson`.`profilepic`
    ,CASE WHEN (SELECT COUNT(*) FROM `tblp_to_p_link` 
    WHERE `idperson_from` = `tblperson`.`idperson` AND `idrelation`=1) > 0  
    THEN true ELSE false END AS `haschild`
    ,(SELECT `tp`.`personname` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner`
    ,(SELECT `tp`.`nickname` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_nickname`
    ,(SELECT `tp`.`dateofbirth` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_dateofbirth`
	,(SELECT `tp`.`dateofdeath` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_dateofdeath`
	,(SELECT `tf`.`familyname` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
	  INNER JOIN `tblfamily` AS `tf` ON `tf`.`idfamily` = `tp`.`idfamily`
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_familyname`
	,(SELECT `tp`.`gender` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_gender`
	,(SELECT `tp`.`profilepic` FROM `tblperson` AS `tp`
    INNER JOIN `tblp_to_p_link` ON `tp`.`idperson` = `tblp_to_p_link`.`idperson_to`
      AND `tblp_to_p_link`.`idrelation`=2
      WHERE `tblp_to_p_link`.`idperson_from` = `tblperson`.`idperson`
      LIMIT 1
    ) as `partner_profilepic`
	
    FROM `tblp_to_p_link`
	INNER JOIN `tblperson` ON `tblperson`.`idperson` = `tblp_to_p_link`.`idperson_to`
	AND `tblp_to_p_link`.`idrelation` = 1
	INNER JOIN `tblfamily` ON `tblfamily`.`idfamily` = `tblperson`.`idfamily`
	WHERE `tblp_to_p_link`.`idperson_from` = :id
	ORDER BY `dateofbirth` ASC, `idperson` ASC;");

    $STH->bindParam("id", $id);
    $STH->execute();
    $STH->setFetchMode(PDO::FETCH_OBJ);
    $data = array();
    //$data = "{";
    while($row = $STH->fetch()) {
        $d = new DateTime($row->dateofbirth);
        $dp = new DateTime($row->partner_dateofbirth);
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
		
		$objPartner = null;
		if($row->gender == 'Female'){
			$DBHP = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$DBHP->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$STHP = $DBHP->prepare("SELECT `tp`.`idperson`, `tf`.`idfamily`, `tf`.`familyname`
			, `tp`.`personname`
			, `tp`.`nickname`
			, `tp`.`dateofbirth`
			, `tp`.`dateofdeath`
			, `tp`.`gender`
			, `tp`.`profilepic`
			, CASE WHEN (SELECT COUNT(*) FROM `tblp_to_p_link` 
			WHERE `tblp_to_p_link`.`idperson_from` = `tp`.`idperson` AND `tblp_to_p_link`.`idrelation`=1) > 0  
			THEN true ELSE false END AS `haschild`
			FROM `tblperson` AS `tp`
			INNER JOIN `tblfamily` AS `tf` ON `tf`.`idfamily` = `tp`.`idfamily`
			WHERE `idperson` = 
			(SELECT `idperson_from` FROM `tblp_to_p_link` WHERE `idperson_to` = :idperson_to AND `idrelation`=2) LIMIT 1;");
			$STHP->bindParam("idperson_to", $row->idperson);
			$STHP->execute();
			$STHP->setFetchMode(PDO::FETCH_OBJ);
			while($rowp = $STHP->fetch()) {
				//print_r($rowp);
				$objPartner = $rowp;
			}
		}
		
		//print_r($objPartner);
		//echo "<br />";
		
        array_push($data,["id"=>($objPartner != null ? $objPartner->idperson : $row->idperson),
		"familyname"=>$row->familyname,
		"gender"=>$row->gender,
        "profilepic"=>$row->profilepic,
        "name"=>$row->personname,
        "nickname"=>$row->nickname,
        "dob"=> ($row->dateofbirth == null ? $row->dateofbirth : $d->format('d-M-Y')),
		"dod"=> ($row->dateofdeath == null ? $row->dateofdeath : $d->format('d-M-Y')),
        "age"=> ($row->dateofbirth == null ? null : $age),
        "haschild"=>($objPartner != null ? $objPartner->haschild : $row->haschild),
        "partner"=>($objPartner != null ? $objPartner->personname : $row->partner),
        "partner_nickname"=>($objPartner != null ? $objPartner->nickname : $row->partner_nickname),
        "partner_dateofbirth"=>($row->partner_dateofbirth == null ? $row->partner_dateofbirth : $dp->format('d-M-Y')),
		"partner_dateofdeath"=> ($row->partner_dateofdeath == null ? $row->partner_dateofdeath : $d->format('d-M-Y')),
        "partner_age"=> ($row->partner_dateofbirth == null ? null : $agep),
		"partner_familyname"=> ($objPartner != null ? $objPartner->familyname : $row->partner_familyname),
		"partner_gender"=> ($objPartner != null ? $objPartner->gender : $row->partner_gender),
        "partner_profilepic"=> ($objPartner != null ? $objPartner->profilepic : $row->partner_profilepic),
        "children"=>null]);
        //$data .= "'name':'" .$row->personname ."',";
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