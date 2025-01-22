<?php

namespace PluginSDKTestSuite;

require_once __DIR__ . '/ConsoleColor.php';
require_once __DIR__ . '/UnitTest.php';
require_once __DIR__ . '/Options.php';

class TestRunner {
    const LOCAL_TEST_SERVER = 'http://localhost:7080/UnitTestEndpoint.php';

    private $options;

    private $localServerPid = null;

    private $testsStatus = true;

    public function __construct(Options $options) {
        $this->options = $options;

        if ($this->options->url === self::LOCAL_TEST_SERVER) {
            $this->startLocalServer();
        }
    }

    private function startLocalServer() {
        $php = $_SERVER['_'];
        // Starting PHP Build-in server in background
        $this->localServerPid = exec(sprintf("%s -q -S 0.0.0.0:7080 %s/UnitTestEndpoint.php > /dev/null 2>&1 & echo $!", $php, __DIR__));
        if (!$this->localServerPid) {
            ConsoleColor::writeWithColor(ConsoleColor::RED, 'Error: Failed to start PHP Build-in server.');
            exit(-1);
        }

        // give it a second to start
        usleep(200000);

        if (!posix_kill($this->localServerPid, 0)) {
            ConsoleColor::writeWithColor(ConsoleColor::RED, 'Error: Failed to start PHP Build-in server.');
            exit(-1);
        }
    }

    public function __destruct() {
        if ($this->localServerPid === null) {
            return;
        }
        $status = 0;
        posix_kill($this->localServerPid, SIGHUP);
        pcntl_waitpid($this->localServerPid, $status, WNOHANG);
    }

    public function run(string $testName): void {
        $testName = basename($testName, '.php');
        $testFile = dirname(__DIR__).'/testCases/'.$testName.'.php';
        if (!file_exists($testFile)) {
            UnitTest::writeWithColor(ConsoleColor::RED, sprintf('Unable to read test file %s.', $testName));
            return;
        }
        $options = $this->options;
        $unitTest = require $testFile;
        try {
            $this->testsStatus = $unitTest->runTest() && $this->testsStatus;
        } catch (\RuntimeException $e) {
            UnitTest::writeWithColor(ConsoleColor::RED, $e->getMessage());
            $this->testsStatus = false;
        }
    }

    public function getTestsStatus(): bool {
        return $this->testsStatus;
    }
}
