<?php
	function authenticate($requestbody, $responsebody, $sqlconn) {
		$IMEI = $requestbody["IMEI"];
		$query = "SELECT * FROM `Users` WHERE `IMEI` = $IMEI";
		$query_response = $sqlconn -> query($query);
		$row = $query_response->fetch_assoc();
		if ($row == null) {
			$query = "INSERT INTO `Users` (`Serial`, `IMEI`, `Coin_Balance`, `Today_Earning`, `Limit_Earning`) VALUES (0, '$IMEI', 0, 0, 0)";
			$sqlconn->query($query);
		}
		return $responsebody;
	}
	
	function balanceq($requestbody, $responsebody, $sqlconn) {
		$IMEI = $requestbody["IMEI"];
		$query = "SELECT * FROM `Users` WHERE `IMEI` = $IMEI";
		$query_response = $sqlconn -> query($query);
		$row = $query_response->fetch_assoc();
		if ($row == null) {
			$responsebody["SUCCESS"] = false;
		} else {
			$balance = $row["Coin_Balance"];
			$responsebody["BALANCE"] = $balance;
		}
		return $responsebody;
	}
	
	function load($requestbody, $responsebody, $sqlconn) {
		$IMEI = $requestbody["IMEI"];
		$query = "SELECT * FROM `Users` WHERE `IMEI` = $IMEI";
		$query_response = $sqlconn -> query($query);
		$row = $query_response->fetch_assoc();
		if ($row == null) {
			$responsebody["SUCCESS"] = false;
		} else {
			$radius = $requestbody["RAD"];
			$lon = $requestbody["COORD"]["LON"];
			$lat = $requestbody["COORD"]["LAT"];
			$alt = $requestbody["COORD"]["ALT"];
			$query = "SELECT Serial, SSID, Cost, SQRT(POWER( 6371000 * acos( cos( radians(Lat) ) * cos(  radians($lat)   ) * cos(  radians(Lon) - radians($lon) ) + sin( radians($lat) )* sin( radians( Lat ) )), 2) + POWER($alt - Alt, 2)) AS DISTANCE FROM `Wifi_Data` HAVING DISTANCE < $radius ORDER BY DISTANCE";
			$query_response  = $sqlconn -> query($query);
			$row = $query_response->fetch_assoc();
			while ($row != null) {
				$wifiobj = array("SERIAL" => intval($row["Serial"]), "SSID"=> $row["SSID"], "COST" => intval($row["Cost"]));
				array_push($responsebody["WIFIS"], $wifiobj);
				$row = $query_response->fetch_assoc();
			}
		}
		return $responsebody;
	}
	
	function buy($requestbody, $responsebody, $sqlconn) {
		$IMEI = $requestbody["IMEI"];
		$serial = $requestbody["WIFI"]["WIFI_SERIAL"];
		$query = "SELECT * FROM `Wifi_Data` WHERE `Serial` = $serial";
		$query1 = "SELECT * FROM `Users` WHERE `IMEI` = '$IMEI'";
		
		$query_response = $sqlconn -> query($query);
		$query_response1 = $sqlconn -> query($query1);
		$row = $query_response->fetch_assoc();
		$row1 = $query_response1->fetch_assoc();
			if($row1 == null) {
				$responsebody["SUCCESS"] = false; 
			} else {
				$wifiobject = array("SERIAL" => intval($row["Serial"]), "SSID"=> $row["SSID"], "PASSWORD" => intval($row["Password"]));
				$fee = $row["Cost"];
				$balance = $row1["Coin_Balance"];
				
				if ($fee > $balance) {
					$responsebody["SUCCESS"] = false;
					return $responsebody;
				}
				
				$updatedbalance = $balance - $fee;
				$updatequery = "UPDATE `Users` SET `Coin_Balance` = $updatedbalance";
				$sqlconn -> query($updatequery);
				array_push($responsebody["WIFIS"], $wifiobject);
				$responsebody["BALANCE"] = $updatedbalance;
			}
		return $responsebody;
	}
	
	function edit($requestbody,$responsebody,$sqlconn) {
    $IMEI = $requestbody["IMEI"];
	$ssid = $requestbody["WIFI"]["SSID"];
	$password = $requestbody["WIFI"]["PASSWORD"];
	$lon = $requestbody["COORD"]["LON"];
	$lat = $requestbody["COORD"]["LAT"];
	$alt = $requestbody["COORD"]["ALT"];
	
    $query = "SELECT Serial, SSID, SQRT(POWER( 6371000 * acos( cos( radians(Lat) ) * cos(  radians($lat)   ) * cos(  radians(Lon) - radians($lon) ) + sin( radians($lat) )* sin( radians( Lat ) )), 2) + POWER($alt - Alt, 2)) AS DISTANCE FROM `Wifi_Data` HAVING (DISTANCE < 100 AND SSID = '$ssid') ORDER BY DISTANCE";
    $query1 = "SELECT * FROM `Users` WHERE IMEI = '$IMEI'";
    $query_response = $sqlconn -> query($query);
    $query_response1 = $sqlconn -> query($query1);
    $row = $query_response->fetch_assoc();
    $row1 = $query_response1->fetch_assoc();
    if($row1 == null) {
        $responsebody["SUCCESS"] = false;
    }
    else if($row == null) {
		$query = "INSERT INTO `Wifi_Data` (`Serial`, `SSID`, `Password`, `Comment`, `Lon`, `Lat`, `Alt`, `Cost`) VALUES (0, '$ssid', '$password', '', $lon, $lat, $alt, 10)";
		$sqlconn->query($query);
		$balance = $row1["Coin_Balance"];
        $payment = $balance + 10;
        $responsebody["BALANCE"] = $payment;
		$query = "UPDATE `Users` SET `Coin_Balance` = $payment WHERE `IMEI` = $IMEI";
		$sqlconn->query($query);
    } else {
		$wifiserial = $row["Serial"];
		$query = "UPDATE `Wifi_Data` SET SSID = '$ssid', Password = '$password' WHERE Serial = $wifiserial";
		$sqlconn->query($query);
		$balance = $row1["Coin_Balance"];
        $payment = $balance + 10;
        $responsebody["BALANCE"] = $payment;
		$query = "UPDATE `Users` SET `Coin_Balance` = $payment WHERE `IMEI` = $IMEI";
		$sqlconn->query($query);
    }
    return $responsebody;
	}
	
	header("Content-type:text/json");
	$address = "fdb17.freehostingeu.com";
	$username = "2346911_main";
	$password = "JawsWorld001";
	$db = "2346911_main";
	
	$mysqlconn = new mysqli($address, $username, $password, $db);
	
	$body = file_get_contents('php://input');
	$jsonbody = json_decode($body, true);

	$response = array("SUCCESS"=>true, "WIFIS"=>[], "BALANCE"=>0);
	
	if ($mysqlconn->connect_error) {
		$response["SUCCESS"] = false;
	} else {
		$request_type = $jsonbody["TYPE"];
		switch ($request_type){
			case "AUTH":
				$res = authenticate($jsonbody, $response, $mysqlconn);
				$response = $res;
				break;
			case "BALANCEQ":
				$res = balanceq($jsonbody, $response, $mysqlconn);
				$response = $res;
				break;
			case "LOAD":
				$res = load($jsonbody, $response, $mysqlconn);
				$response = $res;
				break;
			case "BUY":
				$res = buy($jsonbody, $response, $mysqlconn);
				$response = $res;
				break;
			case "EDIT":
				$res = edit($jsonbody, $response, $mysqlconn);
				$response = $res;
				break;
			break;
			default:
				$response["SUCCESS"] = false;
			break;
		}
	}
	echo json_encode($response);
?>