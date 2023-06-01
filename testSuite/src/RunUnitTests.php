<?php

namespace PluginSDKTestSuite;
include_once __DIR__ . '/ColorCodes.php';
include_once __DIR__ . '/UnitTest.php';

$available_short_options = '';
$available_long_options = [
    'multishop:',
    'api-token::',
    'api-url::',
    'test-name::',
];
$args = getopt($available_short_options, $available_long_options);

if (!isset($args['multishop'])) {
    UnitTest::writeWithColor(ColorCodes::RED, "Please run tests with parameters:\n");

    UnitTest::writeWithColor(ColorCodes::RESET, "Mandatory:");
    foreach ($available_long_options as $option) {
        if (!preg_match('/[^:]:$/', $option)) {
            continue;
        }
        echo sprintf("\t--%s=VALUE", rtrim($option, ':')) . "\n";
    }
    echo "\n";
    UnitTest::writeWithColor(ColorCodes::RESET, "Optional:");
    foreach ($available_long_options as $option) {
        if (!preg_match('/::$/', $option)) {
            continue;
        }
        echo sprintf("\t--%s=VALUE", rtrim($option, ':')) . "\n";
    }
    return;
} elseif (!isset($args['api-token']) && isset($args['api-url'])) {
    UnitTest::writeWithColor(
        ColorCodes::YELLOW,
        "You added a target url but not a token! The tests could fail because of this! \n"
        . "If parsing the tests response fails you probably entered a wrong target url... \n\n"
    );
}

$multishop = $args['multishop'] == 'true';
$apiUrl = $args['api-url'] ?? "http://localhost:7080/UnitTestEndpoint.php";
$apiToken = $args['api-token'] ?? null;

$unitTest = new UnitTest($apiUrl, $apiToken, $multishop);

if (!isset($args['test-name'])) {
    foreach (glob(__DIR__ . '/../testCases/*.json') as $fileName) {
        $unitTest->runTest($fileName);
    }

    UnitTest::writeWithColor(ColorCodes::WHITE, "\n============================================");

    if ($unitTest->getTestsStatus()) {
        UnitTest::writeWithColor(ColorCodes::GREEN, "All tests passed successful!");
    } else {
        UnitTest::writeWithColor(ColorCodes::RED, "At least one test did not pass successful!");
    }
} else {
    $unitTest->runTest(__DIR__ . sprintf('/../testCases/%s.json', $args['test-name']));
}
