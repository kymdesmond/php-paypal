<?php
/**
 * Class Mpesa
 * @package Safaricom\Mpesa
 */
define("MPESA_ENV", "sandbox");
define("MPESA_CONSUMER_KEY", "b5ynoZLKK2vEGcmDT2zOFw6fRecikilT");
define("MPESA_CONSUMER_SECRET", "LnrDNLai9kWc1XRy");
define('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
define('MPESA_LNM_BUS_SHORTCODE', '174379');
define('INITIATOR_NAME', 'apitest486');
define('SECURITY_CREDENTIAL', 'VJd+owOOyHQZa1VzkHA4nuR/EcXJ1yDTEJBF0xqlfAdxKfNkTpuSfuaEcLOEueRyssQVIODk9rRJV1aGmHg0E5GD23GgHO70Lf/SLy18DWzseyPep/6+HDY2fdsn51Af6Vu3G7sAYGVWyaCcx2zcKHVc+FhP5Wk8EuzWGT/MXJXZRDPFzvUnJQOheuCBDxvpvRFK+7EdZjhUZQp62fBbwRTknfRitCpM4eGkPoAfOYdZnNBSi9pFjhFf6WRLqdGnQNVUDUpfDAEERPNB2ocjyoyJuMooAVk7xCzkQxSp7G/dpOzgdjL37KXWD5DkrWlWf8QJCtVA3BFFmW1D90kuHw==');
define('B2C_COMMAND_ID', 'SalaryPayment');

define('HOST', 'https://desmond-payments.herokuapp.com/');
define('B2C_QUERY_TIMEOUT_URL', HOST.'/callback.php');
define('B2C_RESULT_URL', HOST.'/callback.php');
define('B2C_PARTY_A', '601486');
define('B2C_PARTY_B', '254708374149');
// 254708374149
define('SHORTCODE', '601486');
define('C2B_COMMAND_ID', 'CustomerPayBillOnline');
define('CallBackURL', HOST.'/callback.php');
define('ValidationURL', HOST.'/validation.php');
define('ConfirmationURL', HOST.'/confirmation.php');
define('LNM_PARTY_B', '174379');

define('TRX_STATUS_COMMMAND_ID', 'TransactionStatusQuery');
define('TrxStatus_IdentifierType', 1);

class Mpesa
{
    /**
     * This is used to generate tokens for the live environment
     * @return mixed
     */

    public function getCurrentRequestTime(){
        date_default_timezone_set('UTC');
        $date = new \DateTime();
        return $date->format('YmdHis');
    }

    public function getPassword($shortcode, $passkey, $timestamp){
        return base64_encode($shortcode.$passkey.$timestamp);
    }

    public static function generateLiveToken(){
        
        try {
            $consumer_key =MPESA_CONSUMER_KEY;
            $consumer_secret = MPESA_CONSUMER_SECRET;
        } catch (\Throwable $th) {
            $consumer_key = MPESA_CONSUMER_KEY;
            $consumer_secret = MPESA_CONSUMER_SECRET;
        }

        if(!isset($consumer_key)||!isset($consumer_secret)){
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

        return json_decode($curl_response)->access_token;


    }


    /**
     * use this function to generate a sandbox token
     * @return mixed
     */
    public static function generateSandBoxToken(){
        
        try {
            $consumer_key = MPESA_CONSUMER_KEY;
            $consumer_secret = MPESA_CONSUMER_SECRET;
        } catch (\Throwable $th) {
            $consumer_key = MPESA_CONSUMER_KEY;
            $consumer_secret =MPESA_CONSUMER_SECRET;
        }

        if(!isset($consumer_key)||!isset($consumer_secret)){
            die("please declare the consumer key and consumer secret as defined in the documentation");
        }
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);
        // echo $curl_response;
        return json_decode($curl_response)->access_token;
    }

    /**
     * Use this function to initiate a reversal request
     * @param $CommandID | Takes only 'TransactionReversal' Command id
     * @param $Initiator | The name of Initiator to initiating  the request
     * @param $SecurityCredential | 	Encrypted Credential of user getting transaction amount
     * @param $TransactionID | Unique Id received with every transaction response.
     * @param $Amount | Amount
     * @param $ReceiverParty | Organization /MSISDN sending the transaction
     * @param $RecieverIdentifierType | Type of organization receiving the transaction
     * @param $ResultURL | The path that stores information of transaction
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $Occasion | 	Optional Parameter
     * @return mixed|string
     */
    public static function reversal($CommandID, $Initiator, $SecurityCredential, $TransactionID, $Amount, $ReceiverParty, $RecieverIdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks, $Occasion){
        
        try {
            $environment = env("MPESA_ENV");
        } catch (\Throwable $th) {
            $environment = self::env("MPESA_ENV");
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/reversal/v1/request';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/reversal/v1/request';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }



        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'CommandID' => $CommandID,
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'TransactionID' => $TransactionID,
            'Amount' => $Amount,
            'ReceiverParty' => $ReceiverParty,
            'RecieverIdentifierType' => $RecieverIdentifierType,
            'ResultURL' => $ResultURL,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);
        return json_decode($curl_response);

    }

    /**
     * @param $InitiatorName | 	This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request
     * @param $CommandID | Unique command for each transaction type e.g. SalaryPayment, BusinessPayment, PromotionPayment
     * @param $Amount | The amount being transacted
     * @param $PartyA | Organization’s shortcode initiating the transaction.
     * @param $PartyB | Phone number receiving the transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The timeout end-point that receives a timeout response.
     * @param $ResultURL | The end-point that receives the response of the transaction
     * @param $Occasion | 	Optional
     * @return string
     */
    public static function b2c($Amount, $Remarks, $Occasion){
        
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'InitiatorName' => INITIATOR_NAME,
            'SecurityCredential' => SECURITY_CREDENTIAL,
            'CommandID' => B2C_COMMAND_ID ,
            'Amount' => $Amount,
            'PartyA' => B2C_PARTY_A ,
            'PartyB' => B2C_PARTY_B,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => B2C_QUERY_TIMEOUT_URL,
            'ResultURL' => B2C_RESULT_URL,
            'Occasion' => $Occasion
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        echo json_encode($curl_response);

    }
    /**
     * Use this function to initiate a C2B transaction
     * @param $ShortCode | 6 digit M-Pesa Till Number or PayBill Number
     * @param $CommandID | Unique command for each transaction type.
     * @param $Amount | The amount been transacted.
     * @param $Msisdn | MSISDN (phone number) sending the transaction, start with country code without the plus(+) sign.
     * @param $BillRefNumber | 	Bill Reference Number (Optional).
     * @return mixed|string
     */
    public  static  function  c2b($Amount ){
        
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/simulate';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $registerUrl = self::registerUrl();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));

        $curl_post_data = array(
            'ShortCode' => SHORTCODE,
            'CommandID' => C2B_COMMAND_ID,
            'Amount' => $Amount,
            'Msisdn' => B2C_PARTY_B,
            'BillRefNumber' => 0000
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);

        echo $curl_response;

    }


    /**
     * Use this to initiate a balance inquiry request
     * @param $CommandID | A unique command passed to the M-Pesa system.
     * @param $Initiator | 	This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request
     * @param $PartyA | Type of organization receiving the transaction
     * @param $IdentifierType |Type of organization receiving the transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $ResultURL | 	The path that stores information of transaction
     * @return mixed|string
     */
    public static function accountBalance($CommandID, $Initiator, $SecurityCredential, $PartyA, $IdentifierType, $Remarks, $QueueTimeOutURL, $ResultURL){
        
        try {
            $environment = env("MPESA_ENV");
        } catch (\Throwable $th) {
            $environment = self::env("MPESA_ENV");
        }

        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/accountbalance/v1/query';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header


        $curl_post_data = array(
            'CommandID' => $CommandID,
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'PartyA' => $PartyA,
            'IdentifierType' => $IdentifierType,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'ResultURL' => $ResultURL
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);
        return $curl_response;
    }

    /**
     * Use this function to make a transaction status request
     * @param $Initiator | The name of Initiator to initiating the request.
     * @param $SecurityCredential | 	Encrypted password for the initiator to autheticate the transaction request.
     * @param $CommandID | Unique command for each transaction type, possible values are: TransactionStatusQuery.
     * @param $TransactionID | Organization Receiving the funds.
     * @param $PartyA | Organization/MSISDN sending the transaction
     * @param $IdentifierType | Type of organization receiving the transaction
     * @param $ResultURL | The path that stores information of transaction
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $Remarks | 	Comments that are sent along with the transaction
     * @param $Occasion | 	Optional Parameter
     * @return mixed|string
     */
    public function transactionStatus($TransactionID, $Remarks, $Occasion){
        
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header


        $curl_post_data = array(
            'Initiator' => INITIATOR_NAME,
            'SecurityCredential' => SECURITY_CREDENTIAL,
            'CommandID' => TRX_STATUS_COMMMAND_ID,
            'TransactionID' => $TransactionID,
            'PartyA' => SHORTCODE,
            'IdentifierType' => TrxStatus_IdentifierType,
            'ResultURL' => CallBackURL,
            'QueueTimeOutURL' => CallBackURL,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response = curl_exec($curl);


        echo $curl_response;
    }


    /**
     * Use this function to initiate a B2B request
     * @param $Initiator | This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request.
     * @param $Amount | Base64 encoded string of the B2B short code and password, which is encrypted using M-Pesa public key and validates the transaction on M-Pesa Core system.
     * @param $PartyA | Organization’s short code initiating the transaction.
     * @param $PartyB | Organization’s short code receiving the funds being transacted.
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The path that stores information of time out transactions.it should be properly validated to make sure that it contains the port, URI and domain name or publicly available IP.
     * @param $ResultURL | The path that receives results from M-Pesa it should be properly validated to make sure that it contains the port, URI and domain name or publicly available IP.
     * @param $AccountReference | Account Reference mandatory for “BusinessPaybill” CommandID.
     * @param $commandID | Unique command for each transaction type, possible values are: BusinessPayBill, MerchantToMerchantTransfer, MerchantTransferFromMerchantToWorking, MerchantServicesMMFAccountTransfer, AgencyFloatAdvance
     * @param $SenderIdentifierType | Type of organization sending the transaction.
     * @param $RecieverIdentifierType | Type of organization receiving the funds being transacted.

     * @return mixed|string
     */
    public function b2b($Initiator, $SecurityCredential, $Amount, $PartyA, $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $AccountReference, $commandID, $SenderIdentifierType, $RecieverIdentifierType){
        
        try {
            $environment = env("MPESA_ENV");
        } catch (\Throwable $th) {
            $environment = self::env("MPESA_ENV");
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/b2b/v1/paymentrequest';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/b2b/v1/paymentrequest';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header
        $curl_post_data = array(
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'CommandID' => $commandID,
            'SenderIdentifierType' => $SenderIdentifierType,
            'RecieverIdentifierType' => $RecieverIdentifierType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'AccountReference' => $AccountReference,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'ResultURL' => $ResultURL
        );
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        return $curl_response;

    }

    /**
     * Use this function to initiate an STKPush Simulation
     * @param $BusinessShortCode | The organization shortcode used to receive the transaction.
     * @param $LipaNaMpesaPasskey | The password for encrypting the request. This is generated by base64 encoding BusinessShortcode, Passkey and Timestamp.
     * @param $TransactionType | The transaction type to be used for this request. Only CustomerPayBillOnline is supported.
     * @param $Amount | The amount to be transacted.
     * @param $PartyA | The MSISDN sending the funds.
     * @param $PartyB | The organization shortcode receiving the funds
     * @param $PhoneNumber | The MSISDN sending the funds.
     * @param $CallBackURL | The url to where responses from M-Pesa will be sent to.
     * @param $AccountReference | Used with M-Pesa PayBills.
     * @param $TransactionDesc | A description of the transaction.
     * @param $Remark | Remarks
     * @return mixed|string
     */
    public function STKPushSimulation($Amount, $PhoneNumber, $AccountReference, $TransactionDesc){
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        $BusinessShortCode = MPESA_LNM_BUS_SHORTCODE;
        $LipaNaMpesaPasskey = MPESA_PASSKEY;
        $TransactionType = "CustomerPayBillOnline";
        $timestamp='20'.date("ymdhis");
        $CallBackURL = CallBackURL;
        $PartyB = LNM_PARTY_B;
        $PartyA = $PhoneNumber;
        $Remark = "This is a test transaction";
        $password=base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionType
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response=curl_exec($curl);
        return $curl_response;


    }


    /**
     * Use this function to initiate an STKPush Status Query request.
     * @param $checkoutRequestID | Checkout RequestID
     * @param $businessShortCode | Business Short Code
     * @param $password | Password
     * @param $timestamp | Timestamp
     * @return mixed|string
     */
    public static function STKPushQuery($environment, $checkoutRequestID, $businessShortCode){
        $timestamp = Mpesa::getCurrentRequestTime();
         // getPassword($shortcode, $passkey, $timestamp)
        $password = Mpesa::getPassword($businessShortCode, MPESA_PASSKEY, $timestamp);
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));


        $curl_post_data = array(
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $curl_response = curl_exec($curl);

        return $curl_response;
    }

    /**
     * Register validation and confirmation url
     */

     public function registerUrl(){
        try {
            $environment = MPESA_ENV;
        } catch (\Throwable $th) {
            $environment = MPESA_ENV;
        }
        
        if( $environment =="live"){
            $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
            $token=self::generateLiveToken();
        }elseif ($environment=="sandbox"){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
            $token=self::generateSandBoxToken();
        }else{
            return json_encode(["Message"=>"invalid application status"]);
        }

        // $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header
        
        
        $curl_post_data = array(
          //Fill in the request parameters with valid values
          'ShortCode' => SHORTCODE,
          'ResponseType' => 'Completed',
          'ConfirmationURL' => ConfirmationURL,
          'ValidationURL' => ValidationURL
        );
        
        $data_string = json_encode($curl_post_data);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        print_r($curl_response);
        
        return $curl_response;
     }
    /**
     *Use this function to confirm all transactions in callback routes
     */
    public function finishTransaction($status = true)
    {
        if ($status === true) {
            $resultArray=[
                "ResultDesc"=>"Confirmation Service request accepted successfully",
                "ResultCode"=>"0"
            ];
        } else {
            $resultArray=[
                "ResultDesc"=>"Confirmation Service not accepted",
                "ResultCode"=>"1"
            ];
        }

        header('Content-Type: application/json');

        echo json_encode($resultArray);
    }


    /**
     *Use this function to get callback data posted in callback routes
     */
    public function getDataFromCallback(){
        $callbackJSONData=file_get_contents('php://input');
        return $callbackJSONData;
    }


}