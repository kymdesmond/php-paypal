<?php
include 'Mpesa.php';
$mpesa = new Mpesa;

$TransactionID = 'NGP81HA5ZU';
$Remarks = 'Transaction status';
$Occasion = 'Blah blah blah';

$mpesa->transactionStatus($TransactionID, $Remarks, $Occasion)
?>