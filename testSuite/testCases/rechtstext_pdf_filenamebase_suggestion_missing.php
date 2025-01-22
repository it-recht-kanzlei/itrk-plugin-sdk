<?php
return new class($options) extends \PluginSDKTestSuite\UnitTest {

    public function getTestName(): string
    {
        return 'Filename suggestion missing';
    }

    public function getRequestXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <user_auth_token>TEST_TOKEN</user_auth_token>
  <rechtstext_type>datenschutz</rechtstext_type>
  <rechtstext_pdf>JVBERiAxMjM0</rechtstext_pdf>
  <rechtstext_title>Datenschutzerklärung</rechtstext_title>
  <user_account_id>123</user_account_id>
  <rechtstext_country>DE</rechtstext_country>
  <rechtstext_language>de</rechtstext_language>
  <rechtstext_language_iso639_2b>ger</rechtstext_language_iso639_2b>
  <action>push</action>
  <rechtstext_text>Beispielrechtstext</rechtstext_text>
  <rechtstext_html>HTML Beispieltext</rechtstext_html>
</api>
';
    }

    public function isMultiShopTest(): bool
    {
        return false;
    }

    public function getResult(): array
    {
        return array (
          'status' => 'error',
          'error' => '8',
        );
    }
};
