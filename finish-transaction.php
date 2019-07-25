<?php
include 'SaveTransaction.php';
$save = new Save;
$postData = file_get_contents("log.json");
function saveTransactionData($postData){
    $data = json_decode($postData);
    $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
    $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
    $MpesaReceiptNumber = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
    $Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
    $TransactionDate = $data->Body->stkCallback->CallbackMetadata->Item[3]->Value;
    $PhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;

    echo $data;
}
// $commit = $save->saveTransactionData();
?>