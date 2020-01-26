<?php 
function buy($requstbody, $responsebody, $sqlconn) {
$IMEI = $requestbody["IMEI"];
$query = "SELECT * FROM 'Wifi_Data' ";
$query1 = "SELECT * FROM 'Users' WHERE 'IMEI' = $IMEI";
$query_response = $sqlconn -> query($query);
$query_response1 = $sqlconn -> query($query1);
$row = $query_response->fetch_assoc();
$row1 = $query_response1->fetch_assoc();
if($row1 = null) {
    $responsebody["SUCCESS"] = false; 
}
else{
    $fee = $row["Cost"];
    $pass = $row["Password"];
    $balance = $row1["Coin_Balance"];
    $updatedbalance = $balance - $cost;
    $responsebody["Password"] = $pass;
    $responsebody["Balance"] = $updatedbalance;
}
return $responsebody;
}

function edit($requstbody,$responsebody,$sqlconn,$input)
    # The input is the new wifi(SSID) the user wishes to add 
    {
    $IMEI = $requestbody["IMEI"];
    $query = "SELECT * FROM 'Wifi_Data' ";
    $query1 = "SELECT * FROM 'Users' WHERE 'IMEI' = $IMEI";
    $query_response = $sqlconn -> query($query);
    $query_response1 = $sqlconn -> query($query1);
    $row = $query_response->fetch_assoc();
    $row1 = $query_response1->fetch_assoc();
    if($row1 = null) {
        $responsebody["SUCCESS"] = false;
    }
    else if($row["SSID"] === $input){
        $responsebody["SUCCESS"] = false;
    }
    else{
        $balance = row1["Coin_Balance"];
        $payment = $balance + 10;
        $responsebody["SSID"] = $input;
        $responsebody["Coin_Balance"] = $payment;
    }
    return $responsebody;
}

?>