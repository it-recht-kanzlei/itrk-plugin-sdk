<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

class LTIErrorResult extends \ITRechtKanzlei\LTIResult {
    private $exception;
    private $withTrace = false;

    public function __construct(\Throwable $e, bool $withTrace = false) {
        $this->exception = $e;
        $this->withTrace = $withTrace;
    }

    protected function buildTraceNode(
        SimpleXMLElement $node,
        string $nodeName,
        array $trace
    ): void {
        if (!$this->withTrace) {
            return;
        }
        $pTrace = [];
        foreach ($trace as $frame) {
            unset($frame['args']);
            $pTrace[] = $frame;
            if (isset($frame['class']) && ($frame['class'] === LTI::class)) {
                break;
            }
        }
        if (!empty($pTrace)) {
            $this->buildNode($node, $nodeName, $pTrace);
        }
    }

    protected function buildPreviousExceptionNode(SimpleXMLElement $node, \Throwable $e) {
        $node->addChild('type', get_class($e));
        $node->addChild('message', $e->getMessage());
        $node->addChild('code', $e->getCode());
        $node->addChild('file', $e->getFile());
        $node->addChild('line', $e->getLine());
        $this->buildTraceNode($node, 'trace', $e->getTrace());

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
        $this->buildTraceNode($simpleXml, 'error_trace', $this->exception->getTrace());

        if (($pe = $this->exception->getPrevious()) !== null) {
            $this->buildPreviousExceptionNode($simpleXml->addChild('error_previous'), $pe);
        }
        return $simpleXml;
    }
}
