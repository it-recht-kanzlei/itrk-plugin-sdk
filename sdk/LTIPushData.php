<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

/**
 * Represents a legal text document.update.
 */
class LTIPushData {
    private const API_VERSION = '1';

    /** @var string Legal text type: Legal Notice */
    public const DOCTYPE_IMPRINT              = 'impressum';

    /** @var string Legal text type: General Terms and Conditions */
    public const DOCTYPE_TERMS_AND_CONDITIONS = 'agb';

    /** @deprecated */
    public const DOCTYPE_CAMCELLATION_POLICY  = 'widerruf';

    /** @var string Legal text type: Cancellation Policy */
    public const DOCTYPE_CANCELLATION_POLICY  = 'widerruf';

    /** @var string Legal text type: Data Protection Policy */
    public const DOCTYPE_PRIVACY_POLICY       = 'datenschutz';

    /** @var string[] List of all currently supported legal text types. */
    public const ALLOWED_DOCUMENT_TYPES = [
        self::DOCTYPE_IMPRINT,
        self::DOCTYPE_TERMS_AND_CONDITIONS,
        self::DOCTYPE_CANCELLATION_POLICY,
        self::DOCTYPE_PRIVACY_POLICY
    ];

    /**
     * @var string[] List of legal text types that should be sent to the buyer as part of an
     *               order confirmation email (ideally as a PDF).
     */
    public const DOCTYPES_TO_MAIL = [
        self::DOCTYPE_TERMS_AND_CONDITIONS,
        self::DOCTYPE_CANCELLATION_POLICY
    ];

    private $xmlData = null;

     /**
      * @throws LTIError
      */
    public function __construct(\SimpleXMLElement $xmlData) {
        $this->xmlData = $xmlData;

        $this->validateXml();
    }

    /**
     * This method is only relevant for multishop systems. It returns the multishop
     * identifier selected by the user in the client portal. Use this identifier to
     * determine in which sales channel the legal text should be stored.
     *
     * @return string
     * @throws LTIError
     */
    public function getMultiShopId(): string {
        // Only check this element, if it is explicitly requested.
        // The implementations that are not multishop-capable do not require this parameter to be set.
        $this->validateXmlElement('user_account_id', LTIError::INVALID_USER_ACCOUNT_ID);
        return (string)$this->xmlData->user_account_id;
    }

    /**
     * Type of legal text transferred
     *   - impressum = legal notice
     *   - agb = general terms and conditions
     *   - datenschutz = privacy policy
     *   - widerruf = cancellation policy
     *
     * @return string
     */
    public function getType(): string {
        return (string)$this->xmlData->rechtstext_type;
    }

    /**
     * Returns the title of the transferred legal text in the original language.
     *
     * @return string
     */
    public function getTitle(): string {
        return (string)$this->xmlData->rechtstext_title;
    }

    /**
     * Language of the legal text in ISO 639-1 standard, e.g. “de” for German.
     *
     * @return string
     */
    public function getLanguageIso639_1(): string {
        return (string)$this->xmlData->rechtstext_language;
    }

    /**
     * Language of the legal text in ISO 639-2 bibliographic code (B code) standard,
     * e.g. “ger” for German.
     *
     * @return string
     */
    public function getLanguageIso639_2b(): string {
        return (string)$this->xmlData->rechtstext_language_iso639_2b;
    }

    /**
     * ISO 3166-1-alpha-2, Country, e.g. “DE” for Germany. Is transmitted uppercase.
     * Corresponds to the jurisdiction of the legal text.
     *
     * @return string
     */
    public function getCountry(): string {
        return (string)$this->xmlData->rechtstext_country;
    }

    /**
     * Combined representation of language and country.
     * Attention: It should not be understood in the context of a real locale, as a legal
     * text can also be written in a language that is not spoken in the country (jurisdiction),
     * such as en_DE.
     *
     * @deprecated
     * @return string
     */
    public function getLocale(): string {
        return $this->getLanguageIso639_1().'_'.$this->getCountry();
    }

    /**
     * The legal text in the HTML variant
     *
     * @return string
     */
    public function getTextHtml(): string {
        return (string)$this->xmlData->rechtstext_html;
    }

    /**
     * The legal text in the plain text variant.
     *
     * @return string
     */
    public function getText(): string {
        return (string)$this->xmlData->rechtstext_text;
    }

    /**
     * Returns true if a PDF variant was also transferred for the legal text.
     * Please note that only in this case will all PDF-specific methods return non-empty results.
     *
     * @return bool
     */
    public function hasPdf(): bool {
        return (($this->xmlData->rechtstext_pdf != null) && !empty($this->xmlData->rechtstext_pdf))
            || (($this->xmlData->rechtstext_pdf_url != null) && !empty($this->xmlData->rechtstext_pdf_url));
    }

    /**
     * Suggestion for the file name of the PDF document.
     *
     * @return string
     */
    public function getLocalizedFileName(): string {
        if ($this->hasPdf()) {
            return (string)$this->xmlData->rechtstext_pdf_localized_filenamebase_suggestion;
        }
        return '';
    }

    /**
     * Suggestion for the file name of the PDF document. It is no longer recommended to use
     * this element as a suggestion.
     *
     * @deprecated Use getLocalizedFileName() instead.
     * @return string
     */
    public function getFileName(): string {
        if (isset($this->xmlData->rechtstext_pdf_filenamebase_suggestion)) {
            return (string)$this->xmlData->rechtstext_pdf_filenamebase_suggestion;
        }
        return (string)$this->getLocalizedFileName();
    }

    /**
     * Downloads the pdf file from the specified url.
     *
     * @return string
     * @throws LTIError
     */
    private function downloadPdf(): string {
        $this->validateXmlElement('rechtstext_pdf_url', [], LTIError::INVALID_DOCUMENT_PDF);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, (string)$this->xmlData->rechtstext_pdf_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        $error = null;
        if (($errNo = curl_errno($ch)) !== CURLE_OK) {
            $error = new LTIError(
                sprintf('Unable to download PDF file. cURL error (%d): %s', $errNo, curl_error($ch)),
                LTIError::INVALID_DOCUMENT_PDF
            );
        } elseif (($statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) !== 200) {
            $error = new LTIError(
                sprintf('Unable to download PDF file. HTTP status code: %d.', $statusCode),
                LTIError::INVALID_DOCUMENT_PDF
            );
        }
        curl_close($ch);

        if ($error instanceof LTIError) {
            throw $error;
        }
        return $result;
    }

     /**
      * The legal text in the PDF variant.
      *
      * @throws LTIError
      */
    public function getPdf(): string {
        if (!$this->hasPdf()) {
            throw new LTIError('No pdf available!', LTIError::INVALID_DOCUMENT_PDF);
        }

        if (isset($this->xmlData->rechtstext_pdf) && !empty($this->xmlData->rechtstext_pdf)) {
            $pdfBin = base64_decode($this->xmlData->rechtstext_pdf, true);
        } else {
            $pdfBin = $this->downloadPdf();
        }

        if (substr($pdfBin, 0, 4) != '%PDF') {
            throw new LTIError('Content of PDF file is invalid.', LTIError::INVALID_DOCUMENT_PDF);
        }

        return (string)$pdfBin;
    }

    /**
     * Returns the raw parsed XML-Data. The use of this method is discouraged,
     * as each property you want to use is accessible via a dedicated getter.
     *
     * This method should only be used for debugging purposes.
     *
     * @return \SimpleXMLElement
     */
    public function getXml(): \SimpleXMLElement {
        return $this->xmlData;
    }

    /**
     * @param string $name Element to check
     * @param int $errorCode Error code to be thrown in case of an invalid item.
     * @param array|null $allowedValues List of expected values. If no list is specified,
     *                                  the value is only checked for not empty.
     * @return void
     * @throws LTIError
     */
    private function validateXmlElement(string $name, int $errorCode, array $allowedValues = []): void {
        if (!isset($this->xmlData->$name)) {
            throw new LTIError($name . ' is not set.', $errorCode);
        }
        $value = (string)$this->xmlData->$name;
        if (empty($value)) {
            throw new LTIError($name . ' may not be empty.', $errorCode);
        }
        if (!empty($allowedValues) && !in_array($value, $allowedValues)) {
            throw new LTIError($name . ' contains an invalid value.', $errorCode);
        }
    }

    /**
     * Validates the xml document. In the event of an error, an LTIError is thrown.
     *
     * @return void
     * @throws LTIError
     */
    private function validateXml(): void {
        $this->validateXmlElement('rechtstext_type', LTIError::INVALID_DOCUMENT_TYPE, self::ALLOWED_DOCUMENT_TYPES);
        $this->validateXmlElement('rechtstext_title', LTIError::INVALID_DOCUMENT_TITLE);
        $this->validateXmlElement('rechtstext_language', LTIError::INVALID_DOCUMENT_LANGUAGE);
        $this->validateXmlElement('rechtstext_language_iso639_2b', LTIError::INVALID_DOCUMENT_LANGUAGE);
        $this->validateXmlElement('rechtstext_country', LTIError::INVALID_DOCUMENT_COUNTRY);
        $this->validateXmlElement('rechtstext_text', LTIError::INVALID_DOCUMENT_TEXT);
        $this->validateXmlElement('rechtstext_html', LTIError::INVALID_DOCUMENT_HTML);

        if (((string)$this->xmlData->rechtstext_type !== 'impressum') && $this->hasPdf()) {
            $this->validateXmlElement('rechtstext_pdf_localized_filenamebase_suggestion', LTIError::INVALID_FILE_NAME);
        }
    }
}
