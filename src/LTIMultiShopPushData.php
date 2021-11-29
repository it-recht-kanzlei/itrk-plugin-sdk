<?php 

class LTIMultiShopPushData extends LTIPushData {
    private $postData = null;

    public function __construct($postData) {
        parent::__construct($postData);
        
        $this->checkXmlElementAvailable('user_account_id');
        $this->postData = $postData;
    }

    public function getMutliShopId(): string {
        return (string) $this->postData->user_account_id;
    }
}

?>