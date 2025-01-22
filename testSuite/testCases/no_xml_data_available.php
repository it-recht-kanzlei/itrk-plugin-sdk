<?php
return new class($options) extends \PluginSDKTestSuite\UnitTest {

    public function getTestName(): string
    {
        return 'No xml data';
    }

    public function getRequestXml(): string
    {
        return '';
    }

    public function isMultiShopTest(): bool
    {
        return false;
    }

    public function getResult(): array
    {
        return array (
          'status' => 'error',
          'error' => '12',
        );
    }
};
