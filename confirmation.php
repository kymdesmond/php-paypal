<?php

header("Content-Type: application/json");
$response = '{"ResultCode": 0, "ResultDesc": "Confirmmation accepted successfully", "ThirdPartyTransID": "1234567890"}';

include 'Mpesa.php';
$mpesa = new Mpesa;
    $postData = $mpesa->getDataFromCallback();
    //perform your processing here, e.g. log to file....
    $file = fopen("confirmation.json", "w"); //url fopen should be allowed for this to occur
    if(fwrite($file, $postData) === FALSE)
    {
        fwrite("Error: no data written");
    }

    fwrite("\r\n");
    fclose($file);

    echo $response;
?>