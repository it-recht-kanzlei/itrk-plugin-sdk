# LTI XML Specification

IT-Recht Kanzlei uses XML files to transfer legal texts to our clients' systems.
There are three different actions and the corresponding responses that your system must support.

## Basic structure and authentication

### Request (basic)

```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <action>action</action>
  ...
</api>
```

Each request that is sent to your system contains the API version, the action parameter and values within the root
node `api` that can be used to check that the XML has been sent by the IT-Recht Kanzlei.

The action parameter can be assigned one of the following 3 values:

* `version`
* `getaccountlist`
* `push`

Based on the action to be executed, the XML document can contain further elements that are explained in the
description of the corresponding actions.

### Response (basic)

#### In case of success
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>success</status>
</response>
```

#### In case of error
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>error</status>
    <error>1</error>
    <error_message>Something failed.</error_message>
</response>
```

These are the minimum versions of the response in the event of success and of an error. A list of error codes and
corresponding error messages can be found in the “Error Codes” section.

The error messages in the error_message field can be freely defined and should ideally be supplemented with
instructions to the user on how to rectify the error.

All responses can be enriched with meta elements to provide information about the environment in which your interface
is running. This data can be used by the IT-Recht Kanzlei to provide better assistance to users of the interface
in the event of errors.

Example:
```xml
<meta_shopversion>1.0</meta_shopversion>
<meta_modulversion>1.1.0</meta_modulversion>
```

## Authentication
Two procedures are currently implemented for the authentication of the IT-Recht Kanzlei server against your system.

### Authentication via a token (preferred)
Your system generates an alphanumeric token and stores it. The user stores the token in the IT-Recht Kanzlei's
client portal. With each request to the endpoint of your system, your system checks whether the token sent matches
the stored token. The token is filed in the element `user_auth_token`.

Example:
```xml
<user_auth_token>ME2ssmzzWxOa2HoNgpfeLM14KvJAIKKo</user_auth_token>
```

### Authentication with user name and password
The user of the interface can define a user name and password in your system. The user then stores the same access
data in the IT-Recht Kanzlei's client portal. Whether the password is to be transmitted unencrypted or hashed can be
coordinated with the technical support of IT-Recht Kanzlei.

Example:
```xml
<user_username>Username</user_username>
<user_password>Password</user_password>
```

Alternatively, a different authentication procedure can be integrated on request after consultation with the technical
support of the IT-Recht Kanzlei.

## Action "getversion"
The `getversion` action is generally only used to validate the access data and thus ensure that the interface
is set up correctly.

### Example request
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <action>getversion</action>
</api>
```

### Example response (in case of success)
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <status>success</status>
    <meta_shopversion>1.0</meta_shopversion>
    <meta_modulversion>1.1.0</meta_modulversion>
</response>
```

If your system has any special features that the client portal of IT-Recht Kanzlei must take into account, the answer
can be extended by adding additional meta fields. Please discuss this with the technical support team at IT-Recht Kanzlei.

### Example response (in case of error)
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
Some systems offer more than one sales channel. In this case, the client portal must retrieve a list of all the
sales channels available to the user in your system. The user can then select the sales channel in the client portal
to which the legal text is to be transferred.

In addition, a list of available languages and countries into which sales can be made can be specified for each
sales channel.

It therefore also makes sense to implement the action for systems that only offer one sales channel.

### Example request
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <action>getaccountlist</action>
</api>
```

### Example responses
#### Minimal, for systems without sales channels and without specification of languages and countries:
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

This response informs the client portal of the IT-Recht Kanzlei that your system is not a multishop system and that
there are no restrictions as to which language and for which country the legal texts can be transferred.
In this case, please always transfer a 0 as `accountid` and leave `accountname` empty.

If there are restrictions for your system as to which languages can be transferred, the following elements can
be optionally specified for the account with ID 0:
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

In this example, legal texts can be transferred to in both German and English, with the restriction that the texts
must comply with German law. This means that the English legal texts must be translations of the German texts.
The IT-Recht Kanzlei offers corresponding texts.

It is not necessary to specify both language and country of sale in combination. If there is only one restriction,
you can also submit these separately.

Further information on languages and countries is explained in the section *Action “push”*.

#### For systems with multiple sales channels:
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

The value in `accountid` is a reference from your system that you can use to identify the sales channel for which
the legal text is intended when processing a legal text within your implementation. All alphanumeric values are
accepted. The value of `accountname` should be selected so that the user in the client portal knows for which
sales channel the legal texts are to be transferred when selecting the sales channel. The value of `accountid`
is passed on to your system in unchanged form when the legal text is pushed.

Please note that it is permissible to specify different values for the accepted languages and sales countries
for each sales channel.

For example, if your system maps multilingualism via dedicated sales channels, you can specify for each sales channel
which languages it accepts.

## Action "push"
This action is used to transfer the legal texts.

### Example request
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <action>push</action>
  <user_auth_token>EXAMPLE_TOKEN</user_auth_token>
  <user_account_id>sc-id-01</user_account_id>
  <rechtstext_type>agb</rechtstext_type>
  <rechtstext_type_ucase>AGB</rechtstext_type_ucase>
  <rechtstext_title>General Terms and Conditions</rechtstext_title>
  <rechtstext_country>DE</rechtstext_country>
  <rechtstext_language>en</rechtstext_language>
  <rechtstext_language_iso639_2b>eng</rechtstext_language_iso639_2b>
  <rechtstext_pdf_filenamebase_suggestion>agb</rechtstext_pdf_filenamebase_suggestion>
  <rechtstext_pdf_filename_suggestion>agb.pdf</rechtstext_pdf_filename_suggestion>
  <rechtstext_pdf_localized_filenamebase_suggestion>general-terms-and-conditions</rechtstext_pdf_localized_filenamebase_suggestion>
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
  <rechtstext_text>----------------------------&#13;
General Terms and Conditions&#13;
----------------------------&#13;
&#13;
&#13;
Table of Contents&#13;
-----------------&#13;
1. Scope of Application&#13;
2. Conclusion of the Contract&#13;
&#13;
&#13;
1) Scope of Application&#13;</rechtstext_text>
  <rechtstext_html>&lt;div&gt;&lt;h1&gt;General Terms and Conditions&lt;/h1&gt;

&lt;h2&gt;Table of Contents&lt;/h2&gt;
&lt;ol&gt;
  &lt;li&gt;Scope of Application&lt;/li&gt;
  &lt;li&gt;Conclusion of the Contract&lt;/li&gt;
&lt;/ol&gt;

&lt;h2&gt;1) Scope of Application&lt;/h2&gt;&lt;/div&gt;</rechtstext_html>
</api>
```

### Description of the elements

* `user_account_id` [string]

  The element is only set for systems with multiple sales channels if a list of sales channels has previously been
* retrieved using the `getaccountlist` action and the user has made a selection.

* `rechtstext_type` [string] (impressum | agb | datenschutz | widerruf)

  Type of legal text transferred
  - impressum = legal notice
  - agb = general terms and conditions
  - datenschutz = privacy policy
  - widerruf = cancellation policy

* `rechtstext_type_ucase` [string] (IMPRESSUM | AGB | DATENSCHUTZ | WIDERRUF)

  Type of legal text transferred in the upper case variant

* `rechtstext_title` [string]

  Title of the transferred legal text in the original language

* `rechtstext_country` [string]

  ISO 3166-1-alpha-2, Country, e.g. “DE” for Germany. Is transmitted uppercase. Corresponds to the jurisdiction of the legal text.

* `rechtstext_language` [string]

  ISO 639-1, Language of the legal text, e.g. “de” for German, is transmitted lowercase

* `rechtstext_language_iso639_2b` [string]

  ISO 639-2 bibliographic code (B code), Language of the legal text, e.g. “ger” for German, lowercase

* `rechtstext_pdf_filenamebase_suggestion` [string] _deprecated_

  Suggestion for the file name of the PDF document. It is no longer recommended to use this element as a suggestion.
  It is only transmitted for older systems that use this value.

  Alternatively, use `rechtstext_pdf_localized_filenamebase_suggestion`.

* `rechtstext_pdf_filename_suggestion` [string] _deprecated_

  Identical to `rechtstext_pdf_filenamebase_suggestion` with file extension.

* `rechtstext_pdf_localized_filenamebase_suggestion` [string]

  Suggestion for the file name of the PDF document.

* `rechtstext_pdf_url` [url]

  URL where the PDF version of the legal text can be downloaded.

* `rechtstext_pdf` [text] (base64-encoded)

  PDF version of the legal text as a base64-encoded string.

* `rechtstext_pdf_md5hash` [string]

  MD5 checksum of the PDF document. Can be used to validate the downloaded file.

* `rechtstext_text` [text]

  Plain text variant of the legal text.

* `rechtstext_html` [text]

  HTML variant of the legal text. Please note that all special characters and HTML tags are URL-encoded. 

You do not have to use all elements if your system cannot use this information.

There is currently no PDF version of the legal text with the type “impressum”. Accordingly, the
`rechtstext_pdf_*` elements do not exist when pushing a legal text of this type.

### xample response (in case of success)
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
  <status>success</status>
  <meta_shopversion>1.0</meta_shopversion>
  <meta_modulversion>1.1.0</meta_modulversion>
  <target_url>https://www.example.org/terms-and-conditions/</target_url>
</response>
```

The URL at which the legal text can be accessed can be specified in the `target_url` element. This field is optional.

## Error Codes

Use the error codes to inform the IT-Recht Kanzlei's system why the action or the transfer of the legal texts failed.
The error codes are written in the `error` field and are integers.

If possible, please always include an error message (field `error_message`). You can freely define the content.
However, it should correspond to the error code. If the user of your system is able to rectify the error themselves,
you are welcome to provide information on how to rectify the error within the error message.

| Error Code | Meaning                                                                                                                                                                |
|------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 12         | Error processing XML data (e.g. invalid format).                                                                                                                       |
| 1          | Unknown API Version.                                                                                                                                                   |
| 3          | Error authenticating credentials, `user_auth_token` or `user_username` and `user_password` are not correct.                                                            |
| 10         | Value for `action` is empty or invalid.                                                                                                                                |
| 11         | Value for `user_account_id` is required (multishop system), but is empty or cannot be mapped.                                                                          |
| 18         | Value for `rechtstext_title` is empty.                                                                                                                                 |
| 9          | Value for `rechtstext_language` is empty.                                                                                                                              |
| 82         | The language transmitted in `rechtstext_language` is not available for the selected sales channel.                                                                     |
| 17         | Value for `rechtstext_country` is empty or the country transmitted is not available as a sales country for the selected sales channel.                                 |
| 4          | Value for `rechtstext_type` is empty or the type sent is not supported                                                                                                 |
| 5          | Value for `rechtstext_text` is empty.                                                                                                                                  |
| 6          | Value for `rechtstext_html` is empty.                                                                                                                                  |
| 7          | Value for `rechtstext_pdf` or `rechtstext_pdf_url` is empty or invalid.                                                                                                |
| 8          | Value for `rechtstext_pdf_filename_suggestion`, `rechtstext_pdf_filenamebase_suggestion` is empty or invalid _(deprecated)_.                                           |
| 19         | Value for `rechtstext_pdf_filenamebase_suggestion` is empty or invalid.                                                                                                |
| 20         | The store has been closed and no longer exists.                                                                                                                        |
| 50         | The legal text cannot be saved. Please provide the exact reason in the `error_message` field.                                                                          |
| 51         | The legal text PDF variant cannot be saved. Please provide the exact reason in the `error_message` field.                                                              |
| 80         | The interface configuration on the store side has not yet been fully completed by the user (example: legal text pages from page management not yet manually assigned). |
| 81         | The CMS/text page in the store where the legal text is to be stored was not found.                                                                                     |
| 99         | Other, unspecified error. Collective code for all other errors. This error code should not be used if possible!                                                        |

You can define your own error codes with the number range >= 100. Please inform IT-Recht Kanzlei of the error code
and its meaning. Error codes for other generic errors can also be added to the number range < 100 by agreement.

## Further XML examples

Further examples are contained in the directories [example-xmls](example-xmls)
and [testCases](../testSuite/testCases).

## Data transfer

The IT-Recht Kanzlei sends the XML documents as an HTTP-POST request with the content type `text/xml` to the endpoint
of your system. As soon as your system has processed the request accordingly, your system issues a response
with the same content type in accordance with the specification.

## Testing Tools

To test your implementation of the interface, please read the [README.md](../testSuite/README.md)
in the [testSuite](../testSuite) directory.

You can use the [LTI Test Tool](https://www.it-recht-kanzlei.de/developer/sdk.php) as a further tool for testing.
There you can enter the API URL and the authentication data for your test system and execute requests with
the legal text interface of the IT-Recht Kanzlei.

## Integration of your system into the client portal

As soon as you have completed the integration into your system, please contact the technical support of
IT-Recht Kanzlei. The following information is required to integrate your system into the client portal
of the IT-Recht Kanzlei:

* Endpoint at which your connection is accessible,
* Type of data transfer (form-urlencoded or as XML document),
* Authentication method (token or username/password and password hash algorithm, if applicable)
* Does your system support PDF attachments

If possible, please provide the IT-Recht Kanzlei technicians with test access to your system.
This enables the creation of instructions for future users of the interface and a final quality control.
