<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

class LTIAccountListResult extends \ITRechtKanzlei\LTIResult {

    private $accountList = [];

    /**
     * Use this method to provide information about one of the sales channels. In addition to the
     * identifier, which you can use to uniquely identify the sales channel within your system,
     * and the name of the sales channel, you can specify which languages the sales channel
     * supports and, if applicable, which countries the sales channel is legally aimed at.
     *
     * Except for the identifier and the name of the sales channel, all other details are optional.
     *
     * @param string $id The unique identifier of the sales channel
     * @param string|null $name The name of the sales channel
     * @param array $languages The supported languages of the sales channel (ISO 639) or POSIX Locales.
     * @param array $countries The targeted countries of the sales channel (ISO 3166).
     * @param array $additionalData
     *     Other information on the sales channel that you must provide to the system of the IT-Recht Kanzlei
     *     in order for the client portal to control the selection of sales channels accordingly.
     *     For the correct handling of the additional data, please contact the technical support
     *     of IT-Recht Kanzlei.
     * @return $this
     */
    public function addAccount(
        string $id,
        ?string $name,
        array $languages = [],
        array $countries = [],
        array $additionalData = []
    ): self {
        if (!empty($id) && empty($name)) {
            throw new \InvalidArgumentException('The name of the account may not be empty.');
        }
        $this->accountList[$id] = [
            'name' => $name,
            'locales' => array_filter($languages, function ($v) {
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
            'additionalData' => $additionalData
        ];
        return $this;
    }

    protected function buildXml(): SimpleXMLElement {
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
            if (!empty($account['additionalData'])) {
                $this->buildNode($ac, 'additionaldata', $account['additionalData']);
            }
        }
        return $simpleXml;
    }
}
