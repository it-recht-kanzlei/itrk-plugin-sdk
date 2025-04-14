# Test your implementation for the IT-Recht Kanzlei legal text interface

## TestSuite

These tests are independent of the type of implementation and the programming
language used for your interface, as the interface is tested via web requests.
The tests themselves are executed via php.
Use runTestSuite.php to execute the tests.

### How it works

When runTestSuite.php is called, `src/UnitTestEndpoint.php` is started via the built-in web server of php.
The tests from the testCases directory are then sent to the API URL via web request (php_curl) and the API is tested accordingly.

**Please note: When running the tests, any existing legal texts will be overwritten!**

### Running the TestSuite

```bash
php runTestSuite.php --help
```

### Testing your own implementation

```bash
php runTestSuite.php --api-url=http://www.example.com/itrk-test-api.php --api-token=IhrApiToken
php runTestSuite.php --api-url=http://www.example.com/itrk-test-api.php --api-token=IhrApiToken --user-account-id=1 --test-name=action_invalid
```

Enter the corresponding URL for your API endpoint (`--api-url`).  
Use your correct API token (`--api-token`) - the default here is "TEST_TOKEN".  
For multishop systems, you can specify the target account (sales channel) with
the `--user-account-id` parameter.  
You can execute a single test with the parameter `--test-name`. You can use the
file name from the "testCases" directory as the name (without .json)

### Using curl

Alternatively, you can also use curl to test your interface:

```bash
curl -X POST {URL} -dxml='{XML}'

curl -X POST http://www.example.com/itrk-test-api.php -dxml='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><api><api_version>1.0</api_version><user_auth_token>1234567890</user_auth_token><action>push</action><user_account_id>123</user_account_id><rechtstext_type>datenschutz</rechtstext_type><rechtstext_title>Datenschutzerklaerung</rechtstext_title><rechtstext_country>DE</rechtstext_country><rechtstext_language>de</rechtstext_language><rechtstext_language_iso639_2b>ger</rechtstext_language_iso639_2b><rechtstext_pdf_filenamebase_suggestion>datenschutz</rechtstext_pdf_filenamebase_suggestion><rechtstext_pdf_localized_filenamebase_suggestion>Datenschutzerklaerung.pdf</rechtstext_pdf_localized_filenamebase_suggestion><rechtstext_pdf_filename_suggestion>datenschutz</rechtstext_pdf_filename_suggestion><rechtstext_pdf>JVBERiAxMjM0</rechtstext_pdf><rechtstext_text>Beispielrechtstext</rechtstext_text><rechtstext_html>HTML Beispieltext</rechtstext_html></api>'
```

The XML and the expected response can be found in the json files in the
testCases directory.  
To extract the XML, we have provided `src/XtractXml.php`. This extracts the XML
from the testCase and inserts your API token and `user_account_id` (optional):

```bash
php src/XtractXml.php test-file api-token [user_account_id]
php src/XtractXml.php push_valid IhrApiToken MultiShopId
```

```bash
curl -X POST http://www.example.com/itrk-test-api.php -dxml="$(php ./src/XtractXml.php push_valid IhrApiToken MultiShopId)"
```
