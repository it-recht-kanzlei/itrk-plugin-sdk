<?php
return new class($options) extends \PluginSDKTestSuite\UnitTest {

    public function getTestName(): string
    {
        return 'Api version missing';
    }

    public function getRequestXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<api>
  <user_auth_token>TEST_TOKEN</user_auth_token>
  <action>getversion</action>
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
          'error' => '1',
        );
    }
};
