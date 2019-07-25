<?php
include './Mpesa.php';
$mpesa = new Mpesa;

if (isset($_POST['price']) && isset($_POST['msisdn'])){
    $amount = $_POST['price'];
    $msisdn = $_POST['msisdn'];
    $AccountReference = "Test";
    $TrxDescription = "Purchasing good stuff";

    $mpesapayment = $mpesa->STKPushSimulation(1, $msisdn, $AccountReference, $TrxDescription);
    echo json_encode($mpesapayment);
}else
echo "an unknown error occurred";
?>