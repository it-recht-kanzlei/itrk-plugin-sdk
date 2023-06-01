<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */
namespace ITRechtKanzlei;

use SimpleXMLElement;

require_once __DIR__ . '/LTIResult.php';

class LTIErrorResult extends \ITRechtKanzlei\LTIResult {
    private $errorMessage;
    private $errorCode;

    public function __construct(\Exception $e) {
        $this->errorMessage = $e->getMessage();
        $this->errorCode = $e->getCode();
    }

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = parent::buildXML();
        $simpleXml->status = 'error';
        $simpleXml->addChild('error', $this->errorCode);
        $simpleXml->addChild('error_message', $this->errorMessage);

        return $simpleXml;
    }
}
