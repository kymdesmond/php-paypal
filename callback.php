<?php
include 'Mpesa.php';
$mpesa = new Mpesa;
    $postData = $mpesa->getDataFromCallback();
    //perform your processing here, e.g. log to file....
    $file = fopen("log.json", "w"); //url fopen should be allowed for this to occur
    if(fwrite($file, $postData) === FALSE)
    {
        fwrite("Error: no data written");
    }

    fwrite("\r\n");
    fclose($file);

    echo '{"ResultCode": 0, "ResultDesc": "The service was accepted successfully", "ThirdPartyTransID": "1234567890"}';
?>