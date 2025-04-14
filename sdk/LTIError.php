<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

class LTIError extends \Exception {
    /**
     * @var int
     *     en: Unknown API Version
     *     de: Unbekannte API-Version
     */
    const INVALID_API_VERSION = 1;

    /**
     * @var int
     *     en: Not a real error. Legacy test to confirm the validity of the authentication data.
     *     de: Kein echter Fehler. Legacy Test um Gültigkeit der der Zugangsdaten zu bestätigen.
     */
    const VALID_AUTH_TOKEN = 2;

    /**
     * @var int
     *     en: Error authenticating the credentials, i.e. user_auth_token or
     *         user_username and user_username not correct.
     *     de: Fehler beim Authentifizieren der Zugangsdaten, d.h. user_auth_token oder
     *         user_username und user_username nicht korrekt.
     */
    const INVALID_AUTH_TOKEN = 3;

    /**
     * @var int
     *     en: Value for rechtstext_type is empty or sent type is not supported.
     *     de: Wert für rechtstext_type ist leer oder gesendeter Typ wird nicht unterstützt.
     */
    const INVALID_DOCUMENT_TYPE = 4;

    /**
     * @var int
     *     en: Value for rechtstext_text is empty.
     *     de: Wert für rechtstext_text ist leer.
     */
    const INVALID_DOCUMENT_TEXT = 5;

    /**
     * @var int
     *     en: Value for rechtstext_html is empty.
     *     de: Wert für rechtstext_html ist leer.
     */
    const INVALID_DOCUMENT_HTML = 6;

    /**
     * @var int
     *     en: Value for rechtstext_pdf or rechtstext_pdf_url is empty or invalid.
     *     de: Wert für rechtstext_pdf bzw. rechtstext_pdf_url ist leer oder ungültig.
     */
    const INVALID_DOCUMENT_PDF = 7;

    /**
     * @var int
     *     en: Value for rechtstext_pdf_filenamebase_suggestion is empty or invalid.
     *     de: Wert für rechtstext_pdf_filenamebase_suggestion ist leer oder ungültig.
     */
    const INVALID_FILE_NAME = 8;

    /**
     * @var int
     *     en: Value for rechtstext_language is empty.
     *     de: Wert für rechtstext_language ist leer.
     */
    const INVALID_DOCUMENT_LANGUAGE = 9;

    /**
     * @var int
     *     en: Value for action is empty or invalid.
     *     de: Wert für action ist leer oder ungültig.
     */
    const INVALID_ACTION = 10;

    /**
     * @var int
     *     en: Value for user_account_id is required (multishop system), but is empty or
     *         cannot be mapped to a sales channel.
     *     de: Wert für user_account_id wird benötigt (Multishop-System), ist aber leer oder
     *         kann keinem Sales Channel zugeordnet werden.
     */
    const INVALID_USER_ACCOUNT_ID = 11;

    /**
     * @var int
     *     en: Error parsing the XML data.
     *     de: Fehler beim parsen der XML-Daten.
     */
    const PARSING_ERROR = 12;

    /**
     * @var int
     *     en: Fehlercode nicht mehr in Gebrauch.
     *     de: Error code no longer in use.
     */
    const DEPRECATED_13 = 13;

    /**
     * @var int
     *     en: Value for `rechtstext_country` is empty or the country transmitted is not available as a sales country
     *         for the selected sales channel.
     *     de: Wert für `rechtstext_country` ist leer oder das übermittelte Land steht für den gewählten Verkaufskanal
     *         nicht als Verkaufsland zur Verfügung.
     */
    const INVALID_DOCUMENT_COUNTRY = 17;

    /**
     * @var int
     *     en: Value for `rechtstext_title` is empty.
     *     de: Wert für rechtstext_title ist leer.
     */
    const INVALID_DOCUMENT_TITLE = 18;

    /**
     * @var int
     *     en: Value for `rechtstext_pdf_filenamebase_suggestion` is empty or invalid.
     *     de: Wert für rechtstext_pdf_filenamebase_suggestion ist leer oder ungültig.
     */
    const INVALID_DOCUMENT_PDF_FILENAMEBASE_SUGGESTION = 19;

    /**
     * @var int
     *     en: The store has been closed and no longer exists.
     *     de: Der Shop wurde geschlossen und existiert nicht mehr.
     */
    const SHOP_CLOSED = 20;

    /**
     * @var int
     *     en: The legal text cannot be saved. Please provide the exact reason in the error message.
     *     de: Rechtstext kann nicht gespeichert werden. Bitte geben Sie in der Fehlernachricht den genauen Grund an.
     */
    const SAVE_DOCUMENT_ERROR = 50;

    /**
     * @var int
     *     en: The legal text PDF variant cannot be saved. Please provide the exact reason in the error message.
     *     de: Rechtstext PDF kann nicht gespeichert werden. Bitte geben Sie in der Fehlernachricht den genauen Grund an.
     */
    const SAVE_PDF_ERROR = 51;

    /**
     * @var int
     *     en: The interface configuration on the store side has not yet been fully completed
     *         by the user (examples: Legal text pages from CMS not yet manually assigned,
     *         manual generation of an auth token not yet completed).
     *     de: Die Schnittstellenkonfiguration auf Shopseite wurde noch nicht vollständig
     *         vom Nutzer abgeschlossen (Beispiele: Rechtstexteseiten aus CMS noch nicht
     *         manuell zugeordnet, manuelle Generierung eines Auth-Tokens noch nicht erfolgt).
     */
    const CONFIGURATION_INCOMPLETE = 80;

    /**
     * @var int
     *     en: The CMS/text page in the store where the legal text is to be stored was not found.
     *     de: Die CMS-/Textseite im Shop, in die der Rechtstext abgelegt werden soll, wurde nicht gefunden.
     */
    const CONFIGURATION_DOCUMENT_NOT_FOUND = 81;

    /**
     * @var int
     *     en: The language transmitted in `rechtstext_language` is not available for the selected sales channel.
     *     en: Die in `rechtstext_language` übermittelte Sprache steht für den gewählten Verkaufskanal nicht zur Verfügung.
     */
    const CONFIGURATION_LANGUAGE_NOT_SUPPORTED = 82;

    /**
     * @var int
     *     en: Other, unspecified error. Collective code for all other errors.
     *         This error code should not be used if possible!
     *     de: Sonstiger, nicht näher spezifizierter Fehler. Sammelcode für alle anderen Fehler.
     *         Dieser Fehlercode sollte nach Möglichkeit nicht verwendet werden!
     */
    const UNKNOWN_ERROR = 99;

    // en: You can define your own error codes with the number range >= 100. Please inform IT-Recht Kanzlei of
    //     the error code and its meaning. Error codes for other generic errors can also be added to the
    //     number range < 100 by agreement.

    // de: Eigene Fehlercodes können mit dem Zahlenraum >= 100 definiert werden. Bitte teilen
    //     Sie den Fehlercode und dessen Bedeutung der IT-Recht Kanzlei mit. Fehlercodes für
    //     weitere generische Fehler können nach Absprache auch dem Zahlenraum < 100 hinzugefügt werden.

    private $context = [];

    /**
     * @param array<string,mixed> $context
     *     An associative array which may contain further information about the error.
     *     If this data needs to be processed by IT-Recht Kanzlei, please contact the technical support beforehand.
     * @return $this
     */
    public function addContext(array $context = []): self {
        $this->context = array_replace($this->getContext(), $context);
        return $this;
    }

    /**
     * @return array
     */
    public function getContext() {
        return $this->context;
    }

}
