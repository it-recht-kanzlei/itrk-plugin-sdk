# Testen Sie Ihre Implementation für die IT-Recht Kanzlei Rechtstexteschnittstelle

## TestSuite

Diese Tests sind unabhängig von der Art der Implementation und der verwendeten Programmiersprache Ihrer Schnittstelle,
da die Schnittstelle über Web-Anfragen getestet wird.
Die Tests selbst werden über php ausgeführt.
Verwenden Sie runTestSuite.php zum Ausführen der Tests.

### Funktionsweise

Beim Aufruf der runTestSuite.php wird die `src/UnitTestEndpoint.php` im built-in web server von php gestartet.
Anschließend werden die tests aus dem Verzeichnis testCases per Web-Request (php_curl) an die API-URL geschickt und die
API entsprechend getestet.

**Bitte beachten Sie: Bei der Ausführung der Tests werden eventuell vorhandene Rechtstexte überschrieben!**

### Ausführen der TestSuite

```
php runTestSuite.php --help
```

### Testen Ihrer eigenen Schnittstelle

```
php runTestSuite.php --api-url=http://www.example.com/itrk-test-api.php --api-token=IhrApiToken
php runTestSuite.php --api-url=http://www.example.com/itrk-test-api.php --api-token=IhrApiToken --user-account-id=1 --test-name=action_invalid
```

Geben Sie die entsprechende URL für Ihren API-Endpunkt an ("--api-url").  
Verwenden Sie Ihr korrektes API-Token (`--api-token`) - als Default wird hier "TEST_TOKEN" verwendet.  
Bei Multishop Systemen können sie den Zielaccount (Sales Channel) mit dem Parameter `--user-account-id` angeben.  
Mit dem Parameter `--test-name` können Sie einen einzelnen Test starten. Als name können Sie den Dateinamen aus dem
Verzeichnis "testCases" verwenden (ohne .json).

### Verwendung von curl

Alternativ können Sie auch curl verwenden, um Ihre Schnittstelle zu testen:

```
curl -X POST {URL} -dxml='{XML}'

curl -X POST http://www.example.com/itrk-test-api.php -dxml='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><api><api_version>1.0</api_version><rechtstext_pdf_filenamebase_suggestion>datenschutz</rechtstext_pdf_filenamebase_suggestion><rechtstext_pdf_localized_filenamebase_suggestion>Datenschutzerklaerung.pdf</rechtstext_pdf_localized_filenamebase_suggestion><rechtstext_pdf_filename_suggestion>datenschutz</rechtstext_pdf_filename_suggestion><user_auth_token>3910a691a9364947198394c4117bbe4d</user_auth_token><rechtstext_type>datenschutz</rechtstext_type><rechtstext_pdf>JVBERiAxMjM0</rechtstext_pdf><rechtstext_title>Datenschutzerklaerung</rechtstext_title><user_account_id>123</user_account_id><rechtstext_country>DE</rechtstext_country><rechtstext_language>de</rechtstext_language><rechtstext_language_iso639_2b>ger</rechtstext_language_iso639_2b><action>push</action><rechtstext_text>Beispielrechtstext</rechtstext_text><rechtstext_html>HTML Beispieltext</rechtstext_html></api>'
```

Das XML sowie die jeweils erwartete Antwort können Sie den json-Dateien im Verzeichnis testCases entnehmen.  
Zum Extrahieren des XML haben wir `src/XtractXml.php` bereitgestellt. Damit wird das XML aus dem testCase extrahiert und
Ihr API-Token und `user_account_id` (optional) eingefügt:

```
php src/XtractXml.php test-file api-token [user_account_id]
php src/XtractXml.php push_valid IhrApiToken MultiShopId
```

```
curl -X POST http://www.example.com/itrk-test-api.php -dxml="$(php ./src/XtractXml.php push_valid IhrApiToken MultiShopId)"
```
