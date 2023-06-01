<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */
namespace ITRechtKanzlei;

class LTIPushData {
    protected $postData = null;
    private $allowedDocumentTypes = [
        'agb', 'datenschutz', 'widerruf', 'impressum'
    ];

    public function __construct(\SimpleXMLElement $postData) {
        $this->postData = $postData;

        $this->checkXmlData();
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

    public function getFileName(): string {
        return (string) $this->postData->rechtstext_pdf_filenamebase_suggestion;
    }

    public function getLocalizedFileName(): string {
        return (string) $this->postData->rechtstext_pdf_localized_filenamebase_suggestion;
    }

    public function hasPdf(): bool {
        return ($this->postData->rechtstext_pdf != null) && !empty($this->postData->rechtstext_pdf);
    }

    public function getPdf(): string {
        if (!$this->hasPdf()) {
            throw new \Exception('No pdf available!', LTIError::INVALID_DOCUMENT_PDF);
        }

        $pdfBin = base64_decode($this->postData->rechtstext_pdf, true);
        if (substr($pdfBin, 0, 4) != '%PDF') {
            throw new \Exception('Sent pdf cannot be recognized as such!', LTIError::INVALID_DOCUMENT_PDF);
        }

        return (string) $pdfBin;
    }

    public function getApiVersion(): string {
        return (string) $this->postData->api_version;
    }

    private function checkXmlData() {
        $this->checkXmlElementAvailable('rechtstext_type', $this->allowedDocumentTypes, LTIError::INVALID_DOCUMENT_TYPE);
        if ((string)$this->postData->rechtstext_type !== 'impressum') {
            $this->checkXmlElementAvailable('rechtstext_pdf', null, LTIError::INVALID_DOCUMENT_PDF);
            $this->checkXmlElementAvailable('rechtstext_pdf_filename_suggestion', null, LTIError::INVALID_FILE_NAME);
            $this->checkXmlElementAvailable('rechtstext_pdf_filenamebase_suggestion', null, LTIError::INVALID_FILE_NAME);
            $this->checkXmlElementAvailable('rechtstext_pdf_localized_filenamebase_suggestion', null, LTIError::INVALID_FILE_NAME);
        }
        $this->checkXmlElementAvailable('rechtstext_text', null, LTIError::INVALID_DOCUMENT_TEXT);
        $this->checkXmlElementAvailable('rechtstext_html', null, LTIError::INVALID_DOCUMENT_HTML);
        $this->checkXmlElementAvailable('rechtstext_title', null, LTIError::INVALID_DOCUMENT_TITLE);
        $this->checkXmlElementAvailable('rechtstext_country', null, LTIError::INVALID_DOCUMENT_COUNTRY);
        $this->checkXmlElementAvailable('rechtstext_language', null, LTIError::INVALID_DOCUMENT_LANGUAGE);
        $this->checkXmlElementAvailable('rechtstext_language_iso639_2b', null, LTIError::INVALID_DOCUMENT_LANGUAGE);
    }

    protected function checkXmlElementAvailable($name, $allowedValues = null, $errorCode = 1) {
        try {
            $value = $this->postData->$name;

            if ($allowedValues && !in_array($value, $allowedValues)) {
                throw new \Exception('Value of XML element ' . $name . ' is not as expected!', $errorCode);
            }

            if (empty($value)) {
                throw new \Exception('XML element '. $name . '\'s value is empty!', $errorCode);
            }
        } catch (\Exception $e) {
            throw new \Exception('XML element '. $name . ' not set!', $errorCode);
        }
    }
}
