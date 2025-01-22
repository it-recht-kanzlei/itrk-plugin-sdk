<?php
namespace PluginSDKTestSuite;
include_once __DIR__ . '/src/TestRunner.php';

$available_short_options = '';
$available_long_options = [
    'help',
    'api-token::',
    'api-url::',
    'user-account-id::',
    'test-name::',
    'post-form',
    'verbose'
];
$args = getopt($available_short_options, $available_long_options);

if (isset($args['help'])) {
    ConsoleColor::writeWithColor(ConsoleColor::RESET, "Testsuite Help - Please refer to the readme documents.");

    $hasMandatoryOptions = false;
    foreach ($available_long_options as $option) {
        if (!preg_match('/[^:]:$/', $option)) {
            continue;
        }
        if (!$hasMandatoryOptions) {
            ConsoleColor::writeWithColor(ConsoleColor::RESET, "Mandatory:");
            $hasMandatoryOptions = true;
        }
        echo sprintf("\t--%s=VALUE", rtrim($option, ':')) . "\n";
    }
    echo "\n";
    ConsoleColor::writeWithColor(ConsoleColor::RESET, "Optional:");
    foreach ($available_long_options as $option) {
        if (!preg_match('/::$/', $option)) {
            continue;
        }
        echo sprintf("\t--%s=VALUE", rtrim($option, ':')) . "\n";
    }
    return;
} elseif (!isset($args['api-token']) && isset($args['api-url'])) {
    ConsoleColor::writeWithColor(
        ConsoleColor::YELLOW,
        "You added a target url but not a token! The tests could fail because of this! \n"
            ."If parsing the tests response fails you probably entered a wrong target url... \n\n"
    );
}

$runner = new TestRunner(new Options(
    $args['api-url'] ?? TestRunner::LOCAL_TEST_SERVER,
    $args['api-token'] ?? '',
    $args['user-account-id'] ?? '',
    isset($args['post-form']),
    isset($args['verbose'])
));

if (!isset($args['test-name'])) {
    foreach (glob(__DIR__ . '/testCases/*.php') as $fileName) {
        $runner->run($fileName);
    }

    ConsoleColor::writeWithColor(ConsoleColor::WHITE, "\n============================================");

    if ($runner->getTestsStatus()) {
        ConsoleColor::writeWithColor(ConsoleColor::GREEN, "All tests passed successfully!");
    } else {
        ConsoleColor::writeWithColor(ConsoleColor::RED, "At least one test did not pass successfully!");
    }
} else {
    $runner->run($args['test-name']);
}
