<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

class LTIAccountListResult extends \ITRechtKanzlei\LTIResult {

    private $accountList = [];

    public function addAccount(string $id, ?string $name, array $locales = [], $countries = []): self {
        if (!empty($id) && empty($name)) {
            throw new \InvalidArgumentException('The name of the account may not be empty.');
        }
        $this->accountList[$id] = [
            'name' => $name,
            'locales' => array_filter($locales, function ($v) {
                // Locales should match /^[a-z]{2,3}(_[A-Z][a-z]{3})?(_[A-Z]{2})?$/
                // but a non-empty string is the minimum requirement.
                return is_string($v) && !empty($v);
            }),
            'countries' => array_filter($countries, function ($v) {
                // Countries are expected to be ISo-2 or ISO-3 codes and
                // should match /^[A-Z]{2}[A-Z]?$/
                // but a non-empty string is the minimum requirement.
                return is_string($v) && !empty($v);
            }),
        ];
        return $this;
    }

    public function buildXml(): SimpleXMLElement {
        $simpleXml = parent::buildXML();

        foreach ($this->accountList as $key => $account) {
            $ac = $simpleXml->addChild('account');
            $ac->addChild('accountid', $key);
            $ac->addChild('accountname', $account['name']);
            if (!empty($account['locales'])) {
                $loc = $ac->addChild('locales');
                foreach ($account['locales'] as $locale) {
                    $loc->addChild('locale', $locale);
                }
            }
            if (!empty($account['countries'])) {
                $c = $ac->addChild('countries');
                foreach ($account['countries'] as $country) {
                    $c->addChild('country', $country);
                }
            }
        }
        return $simpleXml;
    }
}
