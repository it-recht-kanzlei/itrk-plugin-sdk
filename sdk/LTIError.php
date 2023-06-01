<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

abstract class LTIError {
    /** @var int Unbekannte API-VERSION */
    const INVALID_API_VERSION = 1;

    /** @var int Fehler beim Authentifizieren des Users, d.h. user_auth_token nicht korrekt */
    const INVALID_AUTH_TOKEN = 3;

    /** @var int Wert für rechtstext_type ist leer oder gesendeter Typ wird nicht unterstützt */
    const INVALID_DOCUMENT_TYPE = 4;

    /** @var int Wert für rechtstext_text ist leer */
    const INVALID_DOCUMENT_TEXT = 5;

    /** @var int Wert für rechtstext_html ist leer */
    const INVALID_DOCUMENT_HTML = 6;

    /** @var int Wert für rechtstext_pdf ist leer oder ungültig */
    const INVALID_DOCUMENT_PDF = 7;

    /** @var int Wert für rechtstext_pdf_filename_suggestion, rechtstext_pdf_filenamebase_suggestion oder rechtstext_pdf_localized_filenamebase_suggestion ist leer oder ungültig */
    const INVALID_FILE_NAME = 8;

    /** @var int Wert für rechtstext_language ist leer */
    const INVALID_DOCUMENT_LANGUAGE = 9;

    /** @var int Wert für action ist leer oder ungültig */
    const INVALID_ACTION = 10;

    /** @var int Wert für user_account_id wird benötigt (Multishop-System), ist aber leer */
    const INVALID_USER_ACCOUNT_ID = 11;

    /** @var int Fehler beim Verarbeiten der XML-POST-Daten */
    const PARSING_ERROR = 12;

    /** @var int Fehler wenn Shop kein Multishop ist aber getaccountlist als action abgefragt wird */
    const NO_MULTISHOP_ERROR = 13;

    /** @var int Wert für rechtstext_country ist leer */
    const INVALID_DOCUMENT_COUNTRY = 17;

    /** @var int Wert für rechtstext_title ist leer */
    const INVALID_DOCUMENT_TITLE = 18;

    /** @var int  Wert für rechtstext_pdf_filenamebase_suggestion ist leer oder ungültig */
    const INVALID_DOCUMENT_PDF_FILENAMEBASE_SUGGESTION = 19;

    /** @var int Shop existiert nicht mehr */
    const SHOP_CLOSED = 20;

    /** @var int Rechtstext kann nicht gespeichert werden */
    const SAVE_DOCUMENT_ERROR = 50;

    /** @var int Rechtstext PDF kann nicht gespeichert werden */
    const SAVE_PDF_ERROR = 51;

    /** @var int Die Schnittstellenkonfiguration auf Shopseite wurde noch nicht vollständig vom Nutzer abgeschlossen (Beispiele: Rechtstexteseiten aus CMS noch nicht manuell zugeordnet, manuelle Generierung eines Auth-Tokens noch nicht erfolgt) */
    const CONFIGURATION_INCOMPLETE = 80;

    /** @var int Die CMS-/Textseite im Shop, in die der Rechtstext abgelegt werden soll, wurde nicht gefunden. */
    const CONFIGURATION_DOCUMENT_NOT_FOUND = 81;

    /** @var int sonstiger/nicht näher spezifizierter Fehler (Sammelcode für alle anderen Fehler) */
    const UNKNOWN_ERROR = 99;

    // Eigene Fehlercodes können mit dem Zahlenraum >= 100 definiert werden. Bitte teilen Sie den Fehlercode und dessen Bedeutung
    // der IT-Recht Kanzlei mit. Generische fehler können nach Absprache auch dem Zahlenraum < 100 hinzugefügt werden.
}
