<?php
include 'Mpesa.php';

$mpesa = new Mpesa;

$b2c = $mpesa->b2c(100, "Remarks", "Kujaribu tu maze");
?>
