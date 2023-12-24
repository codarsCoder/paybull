<?php

class Paybull
{
    private $url, $baseUrl, $appId, $appSecret, $merchantKey, $merchantId;
    private $method = "GET", $htmlBody = 0, $parameters = [], $headers = [];

    public function setTestMerchantWithTestCard()
    {
        //Test Merchantları eklenmiştir.
        $this->setBaseUrl('https://test.paybull.com/ccpayment');
        $this->setAppId('c3d81ae3cc3011ef10dcefa31a458d65');
        $this->setAppSecret('217071ea9f3f2e9b695d8f0039024e64');
        $this->setMerchantKey('$2y$10$w/ODdbTmfubcbUCUq/ia3OoJFMUmkM1UVNBiIQIuLfUlPmaLUT1he');
        $this->setMerchantId('98950');


        return $this;
    }

    public function setTestMerchantWithRealCard()
    {
        $this->setBaseUrl('https://app.paybull.com/ccpayment');
        $this->setAppId('');
        $this->setAppSecret('');
        $this->setMerchantKey('');
        $this->setMerchantId('');

        return $this;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    public function getAppSecret()
    {
        return $this->appSecret;
    }

    public function setMerchantKey($merchantKey)
    {
        $this->merchantKey = $merchantKey;
        return $this;
    }

    public function getMerchantKey()
    {
        return $this->merchantKey;
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    protected function url($endpointOrUrl)
    {
        $this->url = $this->getBaseUrl().$endpointOrUrl;
        if(stristr($endpointOrUrl, "http")){
            $this->url = $endpointOrUrl;
        }

        $url = rtrim($this->url,"/");
        $url = explode("/", $url);
        if( in_array($url[count($url) - 1], ["paySmart3D","payByCardToken"]) ){
            $this->htmlBody = 1;
        }

        return $this->url;
    }

    public function addHeader($name, $value = null)
    {
        $header = $name . ": " .$value;
        if(!in_array($header, $this->headers)){
            $this->headers[] = $header;
        }

        return $this;
    }

    public function addHeaders($headers = [])
    {
        foreach ($headers as $key => $val){
            $header = $key . ": " .$val;
            if(!in_array($header, $this->headers)){
                $this->headers[] = $header;
            }
        }

        return $this;
    }

    public function addParameter($name, $value = null)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function addParameters($parameters = [])
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    public function post($endpointOrUrl)
    {
        $this->method = "POST";
        $this->url($endpointOrUrl);

        return $this;
    }

    public function get($endpointOrUrl)
    {
        $this->method = "GET";
        $this->url($endpointOrUrl);

        return $this;
    }

    private function curl()
    {
        $url = $this->url;
        $headers = $this->headers;
        $parameters = $this->parameters;

        $parameters["headers"] = $headers;

        $options = array (
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_VERBOSE => false,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_POSTFIELDS => json_encode($parameters),
            //CURLOPT_SSL_VERIFYHOST => 0,
            //CURLOPT_SSL_VERIFYPEER => 0,
        );

        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );

        $err = curl_errno( $ch );
        $errmsg = curl_error( $ch );
        $header = curl_getinfo( $ch );
        $rurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close( $ch );


        $responseObject = new stdClass();
        //$responseObject->headers = $header;
        $responseObject->http_code = $header["http_code"];
        $responseObject->errno = $err;
        $responseObject->errmsg = $errmsg;
        //$responseObject->url = $rurl;
        if($this->htmlBody){
            $responseObject->htmlBody = $content;
        }

        $responseObject->response = json_decode(str_replace( array ( "\n", "\r", "\t" ), NULL, $content ));

        return $responseObject;
    }


    public function execute()
    {
        $c = $this->curl();
        return $c;
    }

    public function getToken()
    {
        $this->method = "POST";
        $this->url("/api/token");

        $this->addHeader('Accept', 'application/json');
        $this->addHeader('Content-Type', 'application/json');

        $this->addParameters([
            'app_id' => $this->getAppId(),
            'app_secret' => $this->getAppSecret()
        ]);

        $curlResult = $this->curl();

        if($curlResult->http_code != 200){
            echo json_encode([
                "http_code" => $curlResult->http_code,
                "errno" => $curlResult->errno,
                "errmsg" => $curlResult->errmsg,
                "message" => "Http request failed.",
            ]);
            exit;
        }

        if($curlResult->response->status_code != 100){
            echo json_encode([
                "http_code" => $curlResult->http_code,
                "status_code" => $curlResult->response->status_code,
                "errno" => $curlResult->errno,
                "errmsg" => $curlResult->errmsg,
                "message" => "API request failed.",
            ]);
            exit;
        }

        return $curlResult;
    }

    public function getPos()
    {
        $getToken = $this->getToken();
        $is_2d = 0;

        if($getToken->response->data->is_3d == 0){
            $is_2d = 1;
        }

        $this->method = "POST";
        $this->url("/api/getpos");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameters([
            'merchant_key' => $this->getMerchantKey(),
            'is_2d' => $is_2d
        ]);

        return $this->curl();
    }

    public function getCommissions()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/commissions");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);

        return $this->curl();
    }

    public function getCheckStatus()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/checkstatus");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        return $this->curl();
    }

    public function getPurchaseStatus()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/purchase/status");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        return $this->curl();
    }

    public function refund()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/refund");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        return $this->curl();
    }

    public function installments()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/installments");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        return $this->curl();
    }
    public function paySmart2D()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/paySmart2D");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        $hashKey = $this->generateHashKey($this->parameters["total"], $this->parameters["installments_number"], $this->parameters["currency_code"], $this->getMerchantKey(), $this->parameters["invoice_id"], $this->getAppSecret());
        $this->addParameter('hash_key', $hashKey);

        return $this->curl();
    }
    public function paySmart2DTest()
    {
        $total = 2.00;
        $invoiceId = time() . rand(1000,9999);
        $hash = $this->generateHashKey($total,1,'TRY',$this->getMerchantKey(),$invoiceId, $this->getAppSecret());
        $items = [["name" => "Item 1","price" => 2.00,"qnantity" => 1,"description" =>"Item 1 Description"]];

        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/paySmart2D");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token)
            ->addParameter('merchant_key', $this->getMerchantKey())
            ->addParameter('hash_key', $hash)
            ->addParameter('cc_holder_name', 'John Dao')
            ->addParameter('cc_no', '4508034508034509')
            ->addParameter('expiry_month', '12')
            ->addParameter('expiry_year', '2026')
            ->addParameter('cvv', '000')
            ->addParameter('currency_code', 'TRY')
            ->addParameter('installments_number', 1)
            ->addParameter('total', $total)
            ->addParameter('invoice_id', $invoiceId)
            ->addParameter('invoice_description', 'INVOICE TEST DESCRIPTION')
            ->addParameter('merchant_key', $this->getMerchantKey())
            ->addParameter('name', 'John')
            ->addParameter('surname', 'Dao')
            ->addParameter('items', $items);

        return $this->curl();
    }
    public function paySmart3D()
    {
        $getToken = $this->getToken();

        $this->method = "POST";
        $this->url("/api/paySmart3D");
        $this->addHeader('Authorization', 'Bearer '. $getToken->response->data->token);
        $this->addParameter('merchant_key', $this->getMerchantKey());

        $hashKey = $this->generateHashKey($this->parameters["total"], $this->parameters["installments_number"], $this->parameters["currency_code"], $this->getMerchantKey(), $this->parameters["invoice_id"], $this->getAppSecret());
        $this->addParameter('hash_key', $hashKey);

        return $this->curl();
    }


    /****************** HASH GENERATOR *******************/

    // Hash Key

    public function generateHashKey(
        $total,
        $installment,
        $currency_code,
        $merchant_key,
        $invoice_id,
        $app_secret){

        $data = $total.'|'.$installment.'|'.$currency_code.'|'.$merchant_key.'|'.$invoice_id;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);

        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;
    }


    // Transaction Hash Key
    public function generateTransactionHashKey(
        $merchant_key,
        $date,
        $invoiceid = "",
        $currency_id = "",
        $paymentmethodid = "",
        $minamount = "",
        $maxamount = "",
        $transactionState = ""
    ){

        $data = $date.'|'.$invoiceid.'|'.$currency_id.'|'.$paymentmethodid.'|'.$minamount.'|'.$maxamount.'|'.$transactionState;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($merchant_key);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            "$data", 'aes-256-cbc', "$saltWithPassword", null, $iv
        );
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $hash_key = str_replace('/', '__', $msg_encrypted_bundle);

        return $hash_key;
    }




    /**************** HASH GENERATOR END *****************/

    /**
     * Validate Hash Key
     */

    public function validateHashKey($hashKey, $secretKey)
    {
        $status = $currencyCode = "";
        $total = $invoiceId = $orderId = 0;

        if(!empty($hashKey)){
            $hashKey = str_replace('_', '/', $hashKey);
            $password = sha1($secretKey);

            $components = explode(':', $hashKey);
            if(count($components) > 2){
                $iv = isset($components[0]) ? $components[0] : "";
                $salt = isset($components[1]) ? $components[1] : "";
                $salt = hash('sha256', $password.$salt);
                $encryptedMsg = isset($components[2]) ? $components[2] : "";

                $decryptedMsg = openssl_decrypt($encryptedMsg, 'aes-256-cbc', $salt, null, $iv);

                if(strpos($decryptedMsg, '|') !== false){
                    $array = explode('|', $decryptedMsg);
                    $status = isset($array[0]) ? $array[0] : 0;
                    $total = isset($array[1]) ? $array[1] : 0;
                    $invoiceId = isset($array[2]) ? $array[2] : '0';
                    $orderId = isset($array[3]) ? $array[3] : 0;
                    $currencyCode = isset($array[4]) ? $array[4] : '';
                }
            }
        }

        return [$status, $total, $invoiceId, $orderId, $currencyCode];
    }

}