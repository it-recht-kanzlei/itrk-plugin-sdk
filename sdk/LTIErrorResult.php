<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

class LTIErrorResult extends \ITRechtKanzlei\LTIResult {
    private $exception;

    public function __construct(\Throwable $e) {
        $this->exception = $e;
    }

    protected function buildPreviousExceptionNode(SimpleXMLElement $node, \Throwable $e) {
        $node->addChild('type', get_class($e));
        $node->addChild('message', $e->getMessage());
        $node->addChild('code', $e->getCode());
        $node->addChild('file', $e->getFile());
        $node->addChild('line', $e->getLine());
        if (($pe = $e->getPrevious()) !== null) {
            $this->buildPreviousExceptionNode($node->addChild('previous'), $pe);
        }
    }

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = parent::buildXML();
        $simpleXml->status = 'error';
        $code = $this->exception->getCode();
        if ($code === 0) {
            $code = LTIError::UNKNOWN_ERROR;
        } elseif (!$this->exception instanceof LTIError) {
            $code = 'E'.$code;
        }
        $simpleXml->addChild('error', $code);
        $simpleXml->addChild('error_message', $this->exception->getMessage());
        if (!$this->exception instanceof LTIError) {
            $simpleXml->addChild('error_type', get_class($this->exception));
            $simpleXml->addChild('error_file', $this->exception->getFile());
            $simpleXml->addChild('error_line', $this->exception->getLine());
        } else if (!empty($this->exception->getContext())) {
            $this->buildNode($simpleXml, 'error_context', $this->exception->getContext());
        }
        if (($pe = $this->exception->getPrevious()) !== null) {
            $this->buildPreviousExceptionNode($simpleXml->addChild('error_previous'), $pe);
        }
        return $simpleXml;
    }
}
