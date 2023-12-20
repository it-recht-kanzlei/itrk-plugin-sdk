<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

class LTIPushResult extends \ITRechtKanzlei\LTIResult {
    private $targetUrl;

    public function __construct(?string $targetUrl = null) {
        $this->targetUrl = $targetUrl;
    }

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = parent::buildXML();
        if (!empty($this->targetUrl)) {
            $simpleXml->addChild('target_url', $this->targetUrl);
        }
        return $simpleXml;
    }
}
