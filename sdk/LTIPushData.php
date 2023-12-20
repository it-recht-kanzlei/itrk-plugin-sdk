<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

class LTIPushData {
    public const DOCTYPE_IMPRINT              = 'impressum';
    public const DOCTYPE_TERMS_AND_CONDITIONS = 'agb';
    public const DOCTYPE_CAMCELLATION_POLICY  = 'widerruf';
    public const DOCTYPE_PRIVACY_POLICY       = 'datenschutz';

    protected const ALLOWED_DOCUMENT_TYPES = [
        self::DOCTYPE_IMPRINT,
        self::DOCTYPE_TERMS_AND_CONDITIONS,
        self::DOCTYPE_CAMCELLATION_POLICY,
        self::DOCTYPE_PRIVACY_POLICY
    ];

    public const DOCTYPES_TO_MAIL = [
        self::DOCTYPE_TERMS_AND_CONDITIONS,
        self::DOCTYPE_CAMCELLATION_POLICY
    ];

    protected $xmlData = null;

     /**
      * @throws LTIError
      */
    public function __construct(\SimpleXMLElement $xmlData) {
        $this->xmlData = $xmlData;

        $this->checkXmlData();
    }

    public function getMultiShopId(): string {
        // Only check this element, if it is explicitly requested.
        // The implmenentations that are not multishop capable do not require
        // this parameter to be set.
        $this->checkXmlElementAvailable('user_account_id', null, LTIError::INVALID_USER_ACCOUNT_ID);
        return (string)$this->xmlData->user_account_id;
    }

    public function getTitle(): string {
        return (string)$this->xmlData->rechtstext_title;
    }

    public function getTextHtml(): string {
        return (string)$this->xmlData->rechtstext_html;
    }

    public function getText(): string {
        return (string)$this->xmlData->rechtstext_text;
    }

    public function getLanguageIso639_1(): string {
        return (string)$this->xmlData->rechtstext_language;
    }

    public function getLanguageIso639_2b(): string {
        return (string)$this->xmlData->rechtstext_language_iso639_2b;
    }

    public function getType(): string {
        return (string)$this->xmlData->rechtstext_type;
    }

    public function getCountry(): string {
        return (string)$this->xmlData->rechtstext_country;
    }

    public function getLocale(): string {
        return $this->getLanguageIso639_1().'_'.$this->getCountry();
    }

    public function getFileName(): string {
        return (string)$this->xmlData->rechtstext_pdf_filenamebase_suggestion;
    }

    public function getLocalizedFileName(): string {
        return (string)$this->xmlData->rechtstext_pdf_localized_filenamebase_suggestion;
    }

    public function hasPdf(): bool {
        return ($this->xmlData->rechtstext_pdf != null) && !empty($this->xmlData->rechtstext_pdf);
    }

     /**
      * @throws LTIError
      */
    public function getPdf(): string {
        if (!$this->hasPdf()) {
            throw new LTIError('No pdf available!', LTIError::INVALID_DOCUMENT_PDF);
        }

        $pdfBin = base64_decode($this->xmlData->rechtstext_pdf, true);
        if (substr($pdfBin, 0, 4) != '%PDF') {
            throw new LTIError('Sent pdf cannot be recognized as such!', LTIError::INVALID_DOCUMENT_PDF);
        }

        return (string)$pdfBin;
    }

    public function getApiVersion(): string {
        return (string)$this->xmlData->api_version;
    }

     /**
      * @throws LTIError
      */
    protected function checkXmlData() {
        $this->checkXmlElementAvailable('rechtstext_type', self::ALLOWED_DOCUMENT_TYPES, LTIError::INVALID_DOCUMENT_TYPE);
        if ((string)$this->xmlData->rechtstext_type !== 'impressum') {
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

    protected function checkXmlElementAvailable(string $name, ?array $allowedValues, int $errorCode): void {
        if (!isset($this->xmlData->$name)) {
            throw new LTIError('XML element ' . $name . ' not set!', $errorCode);
        }
        $value = (string)$this->xmlData->$name;
        if (empty($value)) {
            throw new LTIError('XML element ' . $name . '\'s value is empty!', $errorCode);
        }
        if (!empty($allowedValues) && !in_array($value, $allowedValues)) {
            throw new LTIError('Value of XML element ' . $name . ' is not as expected!', $errorCode);
        }
    }
}
