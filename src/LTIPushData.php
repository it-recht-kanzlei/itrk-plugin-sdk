<?php 

class LTIPushData {
    private $postData = null;
    private $allowedDocumentTypes = Array('TermsConditions', 'PrivacyPolicy', 'CancellationRights', 'LegalDisclosure');

    public function __construct($postData) {
        $this->checkXmlData();

        $this->postData = $postData;
    }

    public function getTitle(): string {
        return (string) $this->postData->rechtstext_title;
    }

    public function getTextHtml(): string {
        return (string) $this->postData->rechtstext_html;
    }

    public function getText(): string {
        return (string) $this->postData->rechtstext_text;
    }

    public function getLanguageIso639_1(): string {
        return (string) $this->postData->rechtstext_language;
    }

    public function getLanguageIso639_2b(): string {
        return (string) $this->postData->rechtstext_language_iso639_2b;
    }

    public function getType(): string {
        return (string) $this->postData->rechtstext_type;
    }

    public function getCountry(): string {
        return (string) $this->postData->rechtstext_country;
    }

    public function getApiVersion(): string {
        return (string) $this->postData->api_version;
    }

    public function getUserAccountId(): ?string {
        return $this->multiShop ? (string) $this->postData->user_account_id : null;
    }

    public function buildXml(): SimpleXMLElement {
        $simplXmlElement = new SimpleXMLElement('');

        // TODO

        return $simplXmlElement;
    }

    private function checkXmlData() {
        $this->checkXmlElementAvailable('user_auth_token');
        $this->checkXmlElementAvailable('rechtstext_type', $this->allowedDocumentTypes);
        $this->checkXmlElementAvailable('rechtstext_type_ucase');
        $this->checkXmlElementAvailable('rechtstext_title');
        $this->checkXmlElementAvailable('rechtstext_country');
        $this->checkXmlElementAvailable('rechtstext_language');
        $this->checkXmlElementAvailable('rechtstext_language_iso639_2b');
    }

    protected function checkXmlElementAvailable($name, $allowedValues = null) {
        $value = $this->postData->$name;

		if(!isset($value)){ 
            throw new Exception('Xml element '. $value . ' not set!');
        }

        if($allowedValues && !in_array($value, $allowedValues)) {
            throw new Exception('Value of xml element ' . $name . ' is not as expected!');
        }

        if($value == null || $value == '') {
            throw new Exception('Xml element '. $value . '´s value is empty!');
        }
    }
}

?>