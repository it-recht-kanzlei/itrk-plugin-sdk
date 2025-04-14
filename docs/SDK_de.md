# Beschreibung des SDKs zur Übertragung von Rechtstexten

**Hinweis:** *Dieses Dokument beschreibt das PHP SDK. Für eine Beschreibung des XML Austauschformates lesen Sie
bitte das Dokument [LTI XML Spezifikation](LTI_XML_Specification_de.md).*

Die Daten werden per POST im XML-Format an Ihre Schnittstelle übertragen. Das XML-Dokument ist UTF-8 kodiert.
Aktuell kann das XML-Dokument als `Content-Type: text/xml` übertragen werden (bevorzugt) oder alternativ als
`application/x-www-form-urlencoded` mit dem POST-Parameter `xml` (`$_POST['xml']`).
Andere Vereinbarungen sind möglich.

## Implementierung und Verwendung des PHP-SDKs

Für die Nutzer unserer IT-Recht Kanzlei Schnittstelle mit PHP Backend bieten wir ein SDK an. Das SDK ist
übersichtlich gestaltet und leicht zu verwenden. Zunächst müssen die zwei abstrakten Methoden der Klasse `LTIHandler`
und eine der beiden Authentifizierungsmethoden überschrieben werden.

### Erstellung eines eigenen LTIHandlers

#### preHandleRequest()

Diese Methode kann verwendet werden, um Ressourcen zu initialisieren oder
um Vorbedingungen zu validieren (z.B. Konfigurationseinstellungen), die das
Zielsystem erfüllen muss, um zu funktionieren.

Wenn die erforderlichen Bedingungen nicht erfüllt sind, können Sie hier eine
Exception vom Typ `LTIError` auslösen, die in eine korrekt formatierte Fehlerantwort
umgewandelt wird.

#### Authentifizierungsmethoden

Damit Sie sicherstellen können, dass die Übertragung der Rechtstexte von System der IT-Recht Kanzlei stammen,
können Sie eine der beiden folgenden Methoden implementieren.

##### isTokenValid(string $token): bool

Hier muss die Korrektheit des übermittelten Tokens geprüft werden, wenn das
System mit einem Token arbeiten soll.

Der Token sollte von Ihrem System automatisiert  generiert werden. Um ein zufälliges
Token zu erzeugen können Sie die Methode `LTI::generateToken()` verwenden.
Der Token sollte dem Nutzer Ihrer Schnittstelle angezeigt werden, damit der Nutzer
den Token im Mandantenportal der IT-Recht Kanzlei hinterlegen kann.

Dies ist die bevorzugte Authentifikationsmethode.

##### validateUserPass(string $username, string $password): bool

Hier muss die Korrektheit von einem übermittelten Benutzernamen und Passwort
geprüft werden. Den Benutzernamen und das Passwort können die Benutzer in Ihrem
Plugin selbst vergeben oder es kann von Ihrem Plugin erstellt werden.
Benutzername und Passwort müssen anschließend bei der Einrichtung der Schnittstelle
im Mandantenportal hinterlegt werden.

Bitte teilen Sie dem technischen Support der IT-Recht Kanzlei vorab mit,
ob das Passwort im Klartext oder gehasht (inkl. Algorithmus) übertragen werden soll.

#### handleActionGetAccountList(): LTIAccountListResult

Diese Funktion soll als Ergebnis eine Liste aller Verkaufskanäle (Sales Channels)
Ihres Systems zurückliefern. Für jeden Verkaufskanal ist eine ID (`accountid`)
und der Name (`accountname`) anzugeben. Zusätzlich kann zu jedem Verkaufskanal
eine Liste der verfügbaren Zielsprachen und -länder übergeben werden.

Auch wenn es sich bei Ihrem System nicht um ein Multishop-System handelt, wird
empfohlen diese Funktion zu implementieren, um die unterstützten Zielsprachen
und -länder Ihres Systems bekannt zu machen. Für diesen Anwendungsfall muss nur
ein Verkaufskanal angegeben werden, bei dem die ID mit `0` angeben wird. Der
Accountname kann ebenfalls leer bleiben.

#### handleActionPush(LTIPushData $data): LTIPushResult

Nachdem der Request am Plugin angekommen ist, muss das Dokument in die
entsprechende Zielseite integriert werden. Die Verarbeitung des Dokuments wird
hier vorgenommen. Das Objekt `LTIPushData` verfügt über Getter-Methoden für alle
Eigenschaften des Dokuments, die abgefragt und anschließend weiterverarbeitet
werden können.

Bitte orientieren Sie sich an den in der Klasse `LTIPushData` hinterlegten Doc-Comments
für weitere Informationen.

Sollte Ihr System den Rechtstext nicht verarbeiten können, zum Beispiel weil das Dokument
in einer Sprache verfasst ist, die Ihr System nicht unterstützt, werfen Sie bitte
eine Exception vom Typ `LTIError` mit dem entsprechenden Fehlercode und einer
Fehlernachricht, die es dem Nutzer Ihrer Schnittstelle ermöglicht den Fehler
selbst zu beheben. Für weitere Details lesen Sie bitte den Abschnitt "Fehler-Codes".

### Verwendung der LTI Klasse und des von Ihnen implementierten LTIHandlers.

Die Klasse `LTI` (`LTI.php`) übernimmt die Vorverarbeitung der übermittelten XML-Daten,
wertet die auszuführende Aktion aus und führt einen Großteil der Fehlerbehandlung
durch. Um die Lauffähigkeit des SDKs zu gewährleisten, sollte diese Datei nicht
geändert werden.

Der Konstruktor der LTI-Klasse benötigt drei Parameter.

1. Eine Instanz Ihrer überschriebenen LTIHandler-Klasse
2. Die Version des Systems, das mit ihrer Implementation angesprochen wird
3. Die Version Ihrer Implementation, in dem das SDK verwendet wird

Als letzter Schritt muss die `handleRequest()`-Methode des Objekts der LTI-Klasse aufgerufen
werden. Als Parameter wird das empfangene XML Dokument als String übergeben.

Sowohl Fehlerbehandlung innerhalb des SDKs, als auch das Vorbereiten der Response
für den IT-Recht Kanzlei Server werden vom SDK automatisch behandelt. Der Entwickler kann diese
Aspekte ignorieren.

Eine Beispielimplementierung für die Verwendung des SDKs finden Sie im Verzeichnis
[example-implementation](example-implementation).

## Allgemeines

### Versionierung

Voraussetzungen für die Verarbeitung der Versionsnummern durch das Mandantenportal der
IT-Recht Kanzlei:

* `meta_modulversion`: Vergeben Sie für Ihre Implementation bei Erstersterstellung und bei jedem
  folgenden Update hochzählende Versionsnummern
* `meta_shopversion`: Hier wenn möglich ebenfalls eine durch 
  Vergleichsoperatoren vergleichbare Systemversionsnummer übermitteln (z.B. "2.0" statt
  "XY2" - andernfalls nach Absprache). Teilen Sie uns bitte die Struktur Ihrer
  Versionierung mit (z.B. "major.minor"), damit wir diese intern korrekt verarbeiten können.

### Multishop-Systeme

Falls es sich bei Ihrem System um ein sogenanntes Multishop-System handelt,
d.h. unter eine Administrationsoberfläche existieren mehrere Verkaufskanäle/Dienste,
ist es erforderlich, dass dem Benutzer Ihrer Implementation im Mandantenportal
der IT-Recht Kanzlei zunächst eine Auswahlmöglichkeit angeboten wird, für welchen
Verkaufskanal/Dienst die Rechtstexte übertragen werden sollen.

Die Liste der Verkaufskanäle wird mithilfe der Funktion `handleActionGetAccountList()`
abgerufen.

Bei der anschließenden Übertragung eines Rechtstextes kann für Multishop-Systeme
innerhalb der Methode `handleActionPush()` die vom Benutzer ausgewählte ID des
Verkaufskanals vom `LTIPushData` Objekt mithilfe der Methode `getMultiShopId()`
abgerufen werden.

### Best Practices

* Vergeben Sie für Ihr Modul bei Erstersterstellung und bei jedem folgenden Update
  ordentliche numerische, hochzählende Versionsnummern (z.B. "1.0", "1.1", "1.2", ...).
  Neben klassischen Versionsnummern kann hier auch numerisch das
  Veröffentlichungsdatum genutzt werden (z.B. "20230827", Format YYYYMMDD).
  Nennen Sie die aktuelle Versionsnummer immer in der dem Modul beiliegenden
  Dokumentation / Installationsanleitung und mindestens in der Haupt-Programmdatei.
* Fügen Sie Ihrem Modul-Download oder auf der Download-Seite eine gut verständliche
  Installationsanleitung bei. Berücksichtigen Sie in Ihrer Beschreibung auch
  Sonderfälle (z.B. für ältere Shopversionen).
* Fügen Sie neuen Versionen Ihrer Implementation (Updates) eine Beschreibung für den Nutzer
  bei, wie die Aktualisierung auf die neueste Version durchzuführen ist (diese
  weicht oft von der klassischen Installationsanleitung ab), sofern das Update nicht
  automatisch abläuft (z.B. durch einen Klick auf "Update" im Modul-Store des Shops).
* Fügen Sie neuen Veröffentlichungen Ihrer Implementation (Updates) eine
  changelog.txt o. Ä. bei, um den Nutzer über die Neuerungen und
  Fehlerkorrekturen zu informieren.

### Statuscodes

* **success:** Es hat alles geklappt. Sie bestätigen,
    * dass bei der Abfrage der Version keine Fehler aufgetreten sind
    * dass bei der Abfrage der Accountliste keine Fehler aufgetreten sind
    * dass bei einem Push eines Rechtstextes dieser erfolgreich im Account/Shop des Users publiziert wurde
* **error:** Unabhängig vom Fehlercode wird der Status der Fehler-Response immer "error" sein

### Fehler-Codes

Die Fehler-Codes sind in der Datei LTIError.php dokumentiert. Bitte verwenden Sie,
wenn möglich, nur die in der Datei definierten Fehler-Codes.

Vermeiden Sie die Verwendung des Fehlercodes 99 so gut es geht.

Eigene Fehlercodes können mit dem Zahlenraum >= 100 definiert werden. Bitte teilen
Sie den Fehlercode und dessen Bedeutung der IT-Recht Kanzlei mit. Fehlercodes für
weitere generische Fehler können nach Absprache auch dem Zahlenraum < 100 hinzugefügt werden.

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
der IT-Recht Kanzlei. Folgende Informationen werden benötigt, um Ihr System in das Mandantenportal der Kanzlei
zu integrieren:

* Endpunkt, unter dem Ihre Anbindung erreichbar ist,
* Übertragungsart der Daten (form-urlencoded oder als XML-Dokument),
* Authentifizierungsmethode (Token oder Username/Password und ggf. Password-Hash-Algorithmus)
* Unterstützt ihr System die PDF-Anhänge

Falls möglich stellen Sie den Technikern der IT-Recht Kanzlei bitte einen Testzugang zu Ihrem System zur Verfügung.
Dies Ermöglicht die Erstellung einer Handlungsanleitung für die zukünftigen Nutzer der Schnittstelle und eine
abschließende Qualitätskontrolle.

## Details der Implementierung innerhalb des SDK

Sollten Sie das SDK nicht verwenden können, finden Sie in dem Dokument
[LTI XML Spezifikation](LTI_XML_Specification_de.md) eine vollständige Beschreibung
der XML Requests und Responses inklusive einiger Beispiele.

Weitere Beispiele sind in den Verzeichnissen [example-xmls](example-xmls)
und [testCases](../testSuite/testCases) enthalten.
