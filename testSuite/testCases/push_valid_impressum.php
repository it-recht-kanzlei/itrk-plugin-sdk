<?php
return new class($options) extends \PluginSDKTestSuite\UnitTest {

    public function getTestName(): string
    {
        return 'Push valid legal notice (no file name suggestion added)';
    }

    public function getRequestXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <api_version>1.0</api_version>
  <sdk_php_version>1.2.5</sdk_php_version>
  <action>push</action>
  <user_auth_token>TEST_TOKEN</user_auth_token>
  <rechtstext_type>impressum</rechtstext_type>
  <rechtstext_type_ucase>IMPRESSUM</rechtstext_type_ucase>
  <rechtstext_title>Impressum</rechtstext_title>
  <rechtstext_country>DE</rechtstext_country>
  <rechtstext_language>de</rechtstext_language>
  <rechtstext_language_iso639_2b>ger</rechtstext_language_iso639_2b>
  <rechtstext_text>Impressum Text</rechtstext_text>
  <rechtstext_html>&lt;h1&gt;Impressum HTML&lt;/h1&gt;</rechtstext_html>
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
          'status' => 'success',
        );
    }
};
