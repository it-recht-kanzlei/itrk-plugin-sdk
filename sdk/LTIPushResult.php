<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;


class LTIPushResult extends \ITRechtKanzlei\LTIResult {
    private $targetUrl;

    public function __construct(string $targetUrl) {
        $this->targetUrl = $targetUrl;
    }

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = parent::buildXML();
        $simpleXml->addChild('target_url', $this->targetUrl);
        return $simpleXml;
    }
}
