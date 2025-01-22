<?php
require_once __DIR__.'/Options.php';
require_once __DIR__.'/UnitTest.php';

if (!isset($argv[2])) {
    fwrite(STDERR, 'Usage: php XtractXml.php test-filename YourApiToken [user_account_id]' . PHP_EOL . PHP_EOL);
    exit(1);
}

$filename = basename($argv[1], '.php').'.php';
$filepath = __DIR__ . '/../testCases/' . basename($filename);
if (!is_readable($filepath)) {
    fwrite(STDERR, 'Test file can not be read.' . PHP_EOL . PHP_EOL);
    exit(1);
}
$options = new PluginSDKTestSuite\Options('', $argv[2], $argv[3] ?? '');
$test = require($filepath);

echo PluginSDKTestSuite\UnitTest::formatXmlStr($test->prepareXml());
echo "\n";
exit(0);
