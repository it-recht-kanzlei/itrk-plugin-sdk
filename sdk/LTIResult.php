<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

abstract class LTIResult {

    private $shopVersion;
    private $modulVersion;

    private $metaData = [];

    public function setVersions(string $shopVersion, string $modulVersion): self {
        $this->shopVersion = $shopVersion;
        $this->modulVersion = $modulVersion;
        return $this;
    }

    protected static function sanitizeTagName(string $tagName): string {
        $tagName = preg_replace('/_{3,}/', '__', preg_replace('/[^a-zA-Z0-9_-]/', '__', $tagName));
        if (preg_match('/^([0-9-]|xml)/i', $tagName)) {
            $tagName = '_' . $tagName;
        }
        return $tagName;
    }

    private static function isArraySequential(array $a): bool {
        $i = 0;
        foreach ($a as $k => $void) {
            if ($k !== $i++) {
                return false;
            }
        }
        return true;
    }

    protected function buildNode(SimpleXMLElement $node, string $key, $data): void {
        $key = self::sanitizeTagName($key);
        if (is_object($data) && method_exists($data, '__toString')) {
            $data = $data->__toString();
        }
        if (is_scalar($data) || is_null($data)) {
            $node->addChild($key, is_bool($data) ? ($data ? 'true' : 'false') : $data);
            return;
        }

        if (!is_array($data)) {
            // Ignore everything else.
            return;
        }

        $child = $node->addChild($key);

        if (self::isArraySequential($data)) {
            $subKey = substr($key, -1, 1) === 's' ? substr($key, 0, -1) : 'item';
            foreach ($data as $v) {
                $this->buildNode($child, $subKey, $v);
            }
        } else {
            foreach ($data as $k => $v) {
                $this->buildNode($child, $k, $v);
            }
        }
    }

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><response></response>');
        $simpleXml->addChild('status', 'success');
        $simpleXml->addChild('meta_shopversion', $this->shopVersion);
        $simpleXml->addChild('meta_phpversion', phpversion());
        $simpleXml->addChild('meta_modulversion', $this->modulVersion);
        $simpleXml->addChild('meta_apiversion', LTI::SDK_VERSION);

        if (!empty($this->metaData)) {
            $this->buildNode($simpleXml, 'meta_data', $this->metaData);
        }

        return $simpleXml;
    }

    /**
     *You can use this method to add further information to the answer. If the information is important,
     * please contact the technical support of IT-Recht Kanzlei so that the data can be used accordingly.
     *
     * @param string $key
     * @param $data
     * @return $this
     */
    public function setMetaData(string $key, $data): self {
        $this->metaData[$key] = $data;
        return $this;
    }

    /**
     * Use this method to convert the result into an xml response in the expected format.
     *
     * @return string
     */
    public function __toString(): string {
        return $this->buildXML()->asXML();
    }

}
