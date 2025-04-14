# LTI XML Spezifikation

**Hinweis:** *Dieses Dokument beschreibt die XML Spezifikation. Für eine Beschreibung des SDKs lesen Sie
bitte das Dokument [SDK](SDK_de.md).*

Die IT-Recht Kanzlei nutzt XML Dateien, um Rechtstexte in die Systeme unserer Mandanten zu übertragen.
Es gibt drei verschiedene Aktionen und die entsprechenden Antworten, die Ihr System unterstützen muss.

## Basisstruktur und Authentifikation

### Request (Basis)

```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <action>action</action>
  ...
</api>
```

Jeder Request, der an Ihr System gesendet wird, beinhaltet innerhalb des Root-Nodes `api` die API-Version,
den Action-Parameter und Werte, die genutzt werden können, um zu prüfen, dass das XML von der IT-Recht Kanzlei
gesendet wurde.

Der Action-Parameter kann mit einem der folgenden 3 Werte belegt sein:

* `version`
* `getaccountlist`
* `push`

Basierend auf der auszuführenden Aktion kann das XML-Dokument weitere Elemente enthalten, die in der Beschreibung der
entsprechenden Aktionen erläutert werden.

### Response (Basis)

#### Im Erfolgsfall:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>success</status>
</response>
```

#### Im Fehlerfall:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>error</status>
    <error>1</error>
    <error_message>Something failed.</error_message>
</response>
```

Dies sind die Minimalversionen der Antwort im Erfolgs- und Fehlerfall. Eine Liste der Fehlercodes und entsprechenden
Fehlermeldungen finden Sie im Abschnitt "Fehlercodes".

Die Fehlermeldungen im Feld error_message können frei definiert werden und sollten im Idealfall mit Hinweisen an den
Nutzer ergänzt werden, wie der Fehler zu beheben ist.

Alle Antworten können mit Meta-Elementen angereichert werden, um Auskunft über die Umgebung zu geben, in der Ihre
Schnittstelle läuft. Diese Daten können von der IT-Recht Kanzlei genutzt werden, um im Falle von Fehlern den Nutzern der
Schnittstelle bessere Hilfestellungen zu geben.

Beispiel:
```xml
<meta_shopversion>1.0</meta_shopversion>
<meta_modulversion>1.1.0</meta_modulversion>
```

## Authentifikation
Für die Authentifikation des IT-Recht Kanzlei Servers gegenüber Ihrem System sind aktuell zwei Verfahren implementiert.

### Authentifikation über einen Token (bevorzugt)
Ihr System generiert einen alphanumerischen Token und speichert diesen ab. Der Nutzer hinterlegt den Token im
Mandantenportal der IT-Recht Kanzlei. Bei jedem Request an den Endpunkt Ihres Systems überprüft Ihr System, ob der
gesendete mit dem gespeicherten Token überein stimmt. Der Token wird in dem Element `user_auth_token` hinterlegt.

Beispiel:
```xml
<user_auth_token>ME2ssmzzWxOa2HoNgpfeLM14KvJAIKKo</user_auth_token>
```

### Authentifikation mit Benutzername und Passwort
Der Nutzer der Schnittstelle kann in Ihrem System einen Benutzernamen und ein Passwort festlegen. Dieselben Zugangsdaten
hinterlegt der Nutzer anschließend im Mandantenportal der IT-Recht Kanzlei. Ob das Passwort unverschlüsselt oder
gehasht übertragen werden soll, kann mit dem technischen Support der IT-Recht Kanzlei abgestimmt werden.

Beispiel:
```xml
<user_username>Username</user_username>
<user_password>Password</user_password>
```

Alternativ kann auf Wunsch nach Absprache mit dem technischen Support der IT-Recht Kanzlei auch ein anderes
Authentifizierungsverfahren integriert werden.

## Action "getversion"
Die `getversion` Action wird in der Regel nur genutzt, um die Zugangsdaten zu validieren und dadurch eine korrekte
Einrichtung der Schnittstelle zu gewährleisten.

### Beispiel-Request:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <action>getversion</action>
</api>
```

### Beispiel-Response (Erfolgsfall):
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>success</status>
    <meta_shopversion>1.0</meta_shopversion>
    <meta_modulversion>1.1.0</meta_modulversion>
</response>
```

Sollte Ihr System irgendwelche Besonderheiten haben, die das Mandantenportal der IT-Recht Kanzlei berücksichtigen muss,
kann die Antwort durch entsprechende zusätzliche Meta-Felder erweitert werden. 
Bitte sprechen Sie diese mit dem technischen Support der IT-Recht Kanzlei ab.

### Beispiel-Response (Fehlerfall):
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>error</status>
    <meta_shopversion>1.0</meta_shopversion>
    <meta_modulversion>1.1.0</meta_modulversion>
    <error>3</error>
    <error_message>The authentication token is invalid.</error_message>
</response>
```

## Action "getaccountlist"
Einige Systeme bieten verschiedene Verkaufskanäle an. In dem Fall muss das Mandantenportal eine Liste aller verfügbaren
Verkaufskanäle abrufen, die für den Nutzer in Ihrem System zur Verfügung stehen. Der Nutzer kann im Mandantenportal 
anschließend den Verkaufskanal auswählen, in den der Rechtstext übertragen werden soll.

Zusätzlich kann für jeden Verkaufskanal eine Liste an verfügbaren Sprachen und Ländern, in die verkauft werden kann,
angegeben werden.

Daher ist es auch sinnvoll die Action für Systeme zu implementieren, die nur einen Verkaufskanal anbieten.

### Beispiel-Request:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <action>getaccountlist</action>
</api>
```

### Beispiel-Responses
#### Minimal, für Systeme ohne Verkaufskanäle und ohne Angabe von Sprachen und Ländern:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
  <status>success</status>
  <meta_shopversion>1.0</meta_shopversion>
  <meta_modulversion>1.1.0</meta_modulversion>
  <account>
    <accountid>0</accountid>
    <accountname/>
  </account>
</response>
```

Diese Antwort teilt dem Mandantenportal der IT-Recht Kanzlei mit, dass Ihr System kein Multishop-System ist und dass
es keine Beschränkungen gibt, in welcher Sprache und für welches Land die Rechtstexte übertragen werden können.
Bitte übertragen Sie in diesem Fall als `accountid` immer eine 0 und lassen `accountname` leer.

Sollte es für Ihr System Einschränkungen geben, welche Sprachen übertragen werden können, können für den Account
mit der ID 0 noch folgende Elemente optional angegeben werden:
```xml
  <account>
    <accountid>0</accountid>
    <accountname/>
    <locales>
      <locale>de</locale>
      <locale>en</locale>
    </locales>
    <countries>
      <country>DE</country>
    </countries>
  </account>
```

In diesem Beispiel können Rechtstexte auf Deutsch und Englisch übertragen werden, mit der Einschränkung, dass die Texte
dem deutschen Recht entsprechen müssen. Das bedeutet, dass die englischen Rechtstexte Übersetzungen der deutschen Texte
sein müssen. Die IT-Recht Kanzlei bietet entsprechende Texte an.

Es ist nicht notwendig sowohl Sprache als auch Verkaufsland in Kombination anzugeben. Sollte es nur eine Einschränkung
geben, können Sie diese auch separat übermitteln.

Weitere Informationen zu Sprachen und Ländern werden im Abschnitt *Action "push"* erläutert.

#### Für Systeme mit mehreren Verkaufskanälen:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
  <status>success</status>
  <meta_shopversion>1.0</meta_shopversion>
  <meta_modulversion>1.1.0</meta_modulversion>
  <account>
    <accountid>sc-id-01</accountid>
    <accountname>Apparel</accountname>
  </account>
  <account>
    <accountid>sc-id-02</accountid>
    <accountname>Electronics</accountname>
  </account>
</response>
```

Der Wert in `accountid` ist eine Referenz Ihres Systems, mit der Sie bei der Übertragung eines Rechtstextes innerhalb
Ihrer Implementierung den Verkaufskanal ermitteln können, für den der Rechtstext gedacht ist. Alle alphanumerischen
Werte werden akzeptiert. Der Bezeichner unter `accountname` sollte so gewählt werden, dass der Nutzer im Mandantenportal
bei der Auswahl des Verkaufskanals weiß, für welchen Verkaufskanal die Rechtstexte übertragen werden sollen. Der Wert von
`accountid` wird beim Push des Rechtstextes in unveränderter Form an Ihr System durchgereicht.

Bitte beachten Sie, dass es zulässig ist, für jeden Verkaufskanal unterschiedliche Werte für die akzeptierten Sprachen
und Verkaufsländer anzugeben.

Sollte Ihr System zum Beispiel die Mehrsprachigkeit über dedizierte Verkaufskanäle abbilden, können Sie für jeden
Verkaufskanal angeben, welche Sprachen dieser akzeptiert.

## Action "push"
Mit dieser Action werden die Rechtstexte übertragen.

### Beispiel-Request
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <action>push</action>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <user_account_id>sc-id-01</user_account_id>
  <rechtstext_type>agb</rechtstext_type>
  <rechtstext_type_ucase>AGB</rechtstext_type_ucase>
  <rechtstext_title>Allgemeine Geschäftsbedingungen mit Kundeninformationen</rechtstext_title>
  <rechtstext_country>DE</rechtstext_country>
  <rechtstext_language>de</rechtstext_language>
  <rechtstext_language_iso639_2b>ger</rechtstext_language_iso639_2b>
  <rechtstext_pdf_filenamebase_suggestion>agb</rechtstext_pdf_filenamebase_suggestion>
  <rechtstext_pdf_filename_suggestion>agb.pdf</rechtstext_pdf_filename_suggestion>
  <rechtstext_pdf_localized_filenamebase_suggestion>allgemeine-geschaeftsbedingungen-und-kundeninformationen</rechtstext_pdf_localized_filenamebase_suggestion>
  <rechtstext_pdf_url>https://www.it-recht-kanzlei.de/rechtstexte/m1/181/1/1234567890abcdef1234567890abcdef.pdf</rechtstext_pdf_url>
  <rechtstext_pdf>
    JVBERi0xLjQKJeKCrOKAmsaSCjEgMCBvYmo8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFI+PmVuZG9iagoyIDAgb2JqPD
    wvVHlwZS9QYWdlcy9LaWRzWzMgMCBSXS9Db3VudCAxPj5lbmRvYmoKMyAwIG9iajw8L1R5cGUvUGFnZS9QYXJlbnQgMiAw
    IFIvTWVkaWFCb3hbMCAwIDU5NSA3OTJdL1Jlc291cmNlczw8L0ZvbnQ8PC9GMSA0IDAgUj4+Pj4vQ29udGVudHMgNSAwIF
    I+PmVuZG9iago0IDAgb2JqPDwvVHlwZS9Gb250L1N1YnR5cGUvVHlwZTEvTmFtZS9GMS9CYXNlRm9udC9IZWx2ZXRpY2Ev
    RW5jb2RpbmcvTWFjUm9tYW5FbmNvZGluZz4+ZW5kb2JqCjUgMCBvYmo8PC9MZW5ndGggNDQ+PgpzdHJlYW0KQlQgL0YxID
    IwIFRmIDIyMCA3MDAgVGQgKEhlbGxvIFdvcmxkISkgVGogRVQKZW5kc3RyZWFtCmVuZG9iagp4cmVmCjAgNiAKMDAwMDAw
    MDAwMCA2NTUzNSBmIAowMDAwMDAwMDE1IDAwMDAwIG4gCjAwMDAwMDAwNTggMDAwMDAgbiAKMDAwMDAwMDEwNyAwMDAwMC
    BuIAowMDAwMDAwMjE3IDAwMDAwIG4gCjAwMDAwMDAzMTIgMDAwMDAgbiAKdHJhaWxlcgo8PC9Sb290IDEgMCBSL1NpemUg
    Nj4+CnN0YXJ0eHJlZgo0MDMKJSVFT0Y=
  </rechtstext_pdf>
  <rechtstext_pdf_md5hash>c9a0574850d3a3332d35991143dd08d5</rechtstext_pdf_md5hash>
  <rechtstext_text>-------------------------------------------------------&#13;
Allgemeine Geschäftsbedingungen mit Kundeninformationen&#13;
-------------------------------------------------------&#13;
&#13;
&#13;
Inhaltsverzeichnis&#13;
------------------&#13;
1. Geltungsbereich&#13;
2. Vertragsschluss&#13;
&#13;
&#13;
1) Geltungsbereich&#13;</rechtstext_text>
  <rechtstext_html>&lt;div&gt;&lt;h1&gt;Allgemeine Gesch&amp;auml;ftsbedingungen mit Kundeninformationen&lt;/h1&gt;

&lt;h2&gt;Inhaltsverzeichnis&lt;/h2&gt;
&lt;ol&gt;
&lt;li&gt;Geltungsbereich&lt;/li&gt;
&lt;li&gt;Vertragsschluss&lt;/li&gt;
&lt;/ol&gt;

&lt;h2&gt;1) Geltungsbereich&lt;/h2&gt;&lt;/div&gt;</rechtstext_html>
</api>
```

### Beschreibung der Elemente
* `user_account_id` [string]

  Das Element wird nur für Multishop-Systeme gesetzt, wenn zuvor durch die Action `getaccountlist` eine
  Liste der Verkaufskanäle abgerufen und vom Nutzer eine Auswahl getroffen wurde.

* `rechtstext_type` [string] (impressum | agb | datenschutz | widerruf)

  Art des übertragenen Rechtstextes

* `rechtstext_type_ucase` [string] (IMPRESSUM | AGB | DATENSCHUTZ | WIDERRUF)

  Art des übertragenen Rechtstextes in der Upper-Case Variante

* `rechtstext_title` [string]

  Titel des übertragenen Rechtstextes in Originalsprache

* `rechtstext_country` [string]

  ISO 3166-1-alpha-2, Land, z.B. "DE" für Deutschland, wird uppercase übermittelt. Entspricht der Jurisdiktion des Rechtstextes.

* `rechtstext_language` [string]

  ISO 639-1, Sprache des Rechtstextes, z.B. "de" für Deutsch, wird lowercase übermittelt

* `rechtstext_language_iso639_2b` [string]

  ISO 639-2 bibliographic code (B code), Sprache des Rechtstextes, z.B. "ger" für Deutsch,
  lowercase

* `rechtstext_pdf_filenamebase_suggestion` [string] _veraltet_

  Vorschlag für den Dateinamen des PDF-Dokumentes. Es wird nicht mehr empfohlen dieses Element als Vorschlag zu nutzen.
  Es wird nur noch für ältere Systeme übermittelt, die diesen Wert verwenden.

  Verwenden Sie alternativ `rechtstext_pdf_localized_filenamebase_suggestion`.

* `rechtstext_pdf_filename_suggestion` [string] _veraltet_

  Identisch zu `rechtstext_pdf_filenamebase_suggestion` mit Dateiendung.

* `rechtstext_pdf_localized_filenamebase_suggestion` [string]

  Vorschlag für den Dateinamen des PDF-Dokumentes.

* `rechtstext_pdf_url` [url]

  URL, unter der die PDF-Version des Rechtstextes abgerufen werden kann.

* `rechtstext_pdf` [text] (Base64-kodiert)

  PDF-Version des Rechtstextes als Base64 kodierter String.

* `rechtstext_pdf_md5hash` [string]

  MD5 Prüfsumme vom PDF Dokument. Kann genutzt werden, um die heruntergeladene Datei zu validieren.

* `rechtstext_text` [text]

  Plain-Text-Variante des Rechtstextes.

* `rechtstext_html` [text]

  HTML-Variante des Rechtstextes. Bitte beachten Sie, dass alle Sonderzeichen und HTML Tags URL-encoded sind.

Es müssen nicht alle Elemente von Ihnen genutzt werden, wenn Ihr System diese Informationen nicht verwenden kann.

Aktuell gibt es keine PDF-Version vom Rechtstext mit dem Typ "impressum". Dementsprechend existieren beim Push eines
Rechtstextes von diesem Typen die `rechtstext_pdf_*` Elemente nicht.

### Beispiel-Response (Erfolgsfall)
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
  <status>success</status>
  <meta_shopversion>1.0</meta_shopversion>
  <meta_modulversion>1.1.0</meta_modulversion>
  <target_url>https://www.example.org/terms-and-conditions/</target_url>
</response>
```

In dem Element `target_url` kann die URL angegeben werden, unter der der Rechtstext abgerufen werden kann. Dieses Feld ist Optional.

## Fehlercodes

Mithilfe der Fehlercodes teilen Sie dem System der IT-Recht Kanzlei mit, warum die Aktion bzw. die Übertragung
der Rechtstexte fehlgeschlagen ist. Die Fehlercodes werden in das Feld `error` geschrieben und sind ganzzahlig.

Bitte geben Sie nach Möglichkeit immer auch eine Fehlermeldung (Feld `error_message`) an. Den Inhalt können Sie frei
definieren. Er sollte aber dem Fehlercode entsprechen. Falls der Nutzer Ihres Systems in der Lage ist den Fehler selbst
zu beheben, können Sie gerne innerhalb der Fehlermeldung Hinweise zur Fehlerbehebung geben.

| Fehlercode | Bedeutung                                                                                                                                                                             |
|------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 12         | Fehler beim Verarbeiten der XML-Daten (z. B. ungültiges Format).                                                                                                                      |
| 1          | Unbekannte API-Version.                                                                                                                                                               |
| 3          | Fehler beim Authentifizieren der Zugangsdaten, `user_auth_token` oder `user_username` und `user_password` sind nicht korrekt.                                                         |
| 10         | Wert für `action` ist leer oder ungültig.                                                                                                                                             |
| 11         | Wert für `user_account_id ` wird benötigt (Multishop-System), ist aber leer oder kann nicht zugeordnet werden.                                                                        |
| 18         | Wert für `rechtstext_title` ist leer.                                                                                                                                                 |
| 9          | Wert für `rechtstext_language` ist leer.                                                                                                                                              |
| 82         | Die in `rechtstext_language` übermittelte Sprache steht für den gewählten Verkaufskanal nicht zur Verfügung.                                                                          |
| 17         | Wert für `rechtstext_country` ist leer oder das übermittelte Land steht für den gewählten Verkaufskanal nicht als Verkaufsland zur Verfügung.                                         |
| 4          | Wert für `rechtstext_type` ist leer oder der gesendete Typ wird nicht unterstützt.                                                                                                    |
| 5          | Wert für `rechtstext_text` ist leer.                                                                                                                                                  |
| 6          | Wert für `rechtstext_html` ist leer.                                                                                                                                                  |
| 7          | Wert für `rechtstext_pdf` bzw. `rechtstext_pdf_url` ist leer oder ungültig.                                                                                                           |
| 8          | Wert für `rechtstext_pdf_filename_suggestion`, `rechtstext_pdf_filenamebase_suggestion` ist leer oder ungültig _(veraltet)_.                                                          |
| 19         | Wert für `rechtstext_pdf_filenamebase_suggestion` ist leer oder ungültig.                                                                                                             |
| 20         | Der Shop wurde geschlossen und existiert nicht mehr.                                                                                                                                  |
| 50         | Rechtstext kann nicht gespeichert werden. Bitte geben Sie in dem Feld `error_message` den genauen Grund an.                                                                           |
| 51         | Rechtstext PDF kann nicht gespeichert werden. Bitte geben Sie in dem Feld `error_message` den genauen Grund an.                                                                       |
| 80         | Die Schnittstellenkonfiguration auf Shopseite wurde noch nicht vollständig vom Nutzer abgeschlossen (Beispiel: Rechtstexteseiten aus Seitenverwaltung noch nicht manuell zugeordnet). |
| 81         | Die CMS-/Textseite im Shop, in die der Rechtstext abgelegt werden soll, wurde nicht gefunden.                                                                                         |
| 99         | Sonstiger, nicht näher spezifizierter Fehler. Sammelcode für alle anderen Fehler. Dieser Fehlercode sollte nach Möglichkeit nicht verwendet werden!                                   |

Eigene Fehlercodes können mit dem Zahlenraum >= 100 definiert werden. Bitte teilen
Sie den Fehlercode und dessen Bedeutung der IT-Recht Kanzlei mit. Fehlercodes für
weitere generische Fehler können nach Absprache auch dem Zahlenraum < 100 hinzugefügt werden.

## Weitere XML Beisiele

Weitere Beispiele sind in den Verzeichnissen [example-xmls](example-xmls)
und [testCases](../testSuite/testCases) enthalten.

## Übertragung der Daten

Die IT-Recht Kanzlei sendet die XML-Dokumente als HTTP-POST Request mit dem Content-Type `text/xml`
an den Endpunkt Ihres Systems. Sobald Ihr System die Anfrage entsprechend bearbeitet hat, gibt Ihr
System mit demselben Content-Type eine der Spezifikation entsprechende Antwort aus.

## Testwerkzeuge

Um Ihre Implementierung der Schnittstelle testen zu können, lesen Sie bitte die
[README_de.md](../testSuite/README_de.md) im Verzeichnis [testSuite](../testSuite).

Als weiteres Werkzeug zum Testen können Sie das
[LTI Test Tool](https://www.it-recht-kanzlei.de/developer/sdk.php) verwenden.
Dort können Sie die API-URL und die Daten zur Authentifikation für ihr
Testsystem hinterlegen und Requests mit der Rechtstexteschnittstelle
der IT-Recht Kanzlei ausführen.

## Integration Ihres Systems in das Mandantenportal

Sobald Sie die Anbindung an Ihr System fertiggestellt haben, kontaktieren Sie bitte den technischen Support
der IT-Recht Kanzlei. Folgende Informationen werden benötigt, um Ihr System in das Mandantenportal
der Kanzlei zu integrieren:

* Endpunkt, unter dem Ihre Anbindung erreichbar ist,
* Übertragungsart der Daten (form-urlencoded oder als XML-Dokument),
* Authentifizierungsmethode (Token oder Username/Password und ggf. Password-Hash-Algorithmus)
* Unterstützt ihr System die PDF-Anhänge

Falls möglich stellen Sie den Technikern der IT-Recht Kanzlei bitte einen Testzugang zu Ihrem System zur Verfügung.
Dies Ermöglicht die Erstellung einer Handlungsanleitung für die zukünftigen Nutzer der Schnittstelle und eine 
abschließende Qualitätskontrolle.

