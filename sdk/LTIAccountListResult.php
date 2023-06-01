<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */
namespace ITRechtKanzlei;

use SimpleXMLElement;

require_once __DIR__ . '/LTIResult.php';

class LTIAccountListResult extends \ITRechtKanzlei\LTIResult {

    private $accountList = [];

    public function addAccount(string $id, string $name): self {
        $this->accountList[$id] = $name;
        return $this;
    }

    public function buildXml(): SimpleXMLElement {
        $simpleXml = parent::buildXML();

        foreach ($this->accountList as $key => $value) {
            $ac = $simpleXml->addChild('account');
            $ac->addChild('accountid', $key);
            $ac->addChild('accountname', $value);
        }

        return $simpleXml;
    }
}
