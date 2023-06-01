<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

class LTIMultiShopPushData extends \ITRechtKanzlei\LTIPushData {
    /**
     * @throws \Exception
     */
    public function __construct(\SimpleXMLElement $postData) {
        parent::__construct($postData);

        $this->checkXmlElementAvailable('user_account_id', null, LTIError::INVALID_USER_ACCOUNT_ID);
    }

    public function getMultiShopId(): string {
        return (string)$this->postData->user_account_id;
    }
}
