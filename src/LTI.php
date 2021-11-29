<?php 
require_once "src/LTIResult.php";

class LTI {

    private $ltiHandler;
    private const SDK_VERSION = '1.1.0';
    private $xmlData;
    private $multiShop = false;

    public function __construct(LTIHandler $ltiHandler) {
        $this->ltiHandler = $ltiHandler;
    }

    public function setMultiShopTrue() {
        $this->multiShop = true;
    }

    public function getSdkVersion(): string {
        return self::SDK_VERSION;
    }

    public function handleRequest($postData) {   
        try {     
            if(function_exists('simplexml_load_string')) {
                $this->xmlData = simplexml_load_string($postData);

                if(!$this->xmlData) {
                    throw new Exception('Error parsing xml value!'); 
                }
            } else {
                throw new Exception('extension SimpleXML not available on host system');
            }


            // $this->checkXmlElementAvailable('user_auth_token');

            if($this->ltiHandler->isTokenValid($this->xmlData->user_auth_token)) {
                $ltiResult = null;

                if('push') {
                    $ltiResult = $this->ltiHandler->handleActionPush(new LTIPushResult($this->xmlData, $this->multiShop));
                } else if('getaccountlist') {
                    $ltiResult = $this->ltiHandler->handleActionGetAccountList();
                } else if ('version') {
                    $ltiResult = $this->ltiHandler->handleActionGetAccountList();
                }

                if($ltiResult) {
                    $this->ltiHandler->sendResponse($ltiResult->toString());
                }

            } else {
                throw new Exception('Invalid user auth token!'); 
            }
        } catch(Exception $e) {
            $this->ltiHandler->sendResponse($e->getMessage());
        }

        return null;
    }
}
?>