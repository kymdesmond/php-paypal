<?php
include 'Mpesa.php';

$mpesa = new Mpesa;

$b2c = $mpesa->c2b(100);
?>