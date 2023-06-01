<?php

$php = $_SERVER['_'];
$scriptDir = dirname(__FILE__);
chdir($scriptDir . '/src/') || exit('Error: Could not change to src directory.');

if (!isset($argv[1])) {
    passthru($php . ' ./RunUnitTests.php');
    exit();
}

// Starting PHP Build-in server in background
$pid = exec(sprintf("%s -q -S 0.0.0.0:7080 UnitTestEndpoint.php > /dev/null 2>&1 & echo $!", $php));
if (!$pid) {
    exit('Error: Failed to start PHP Build-in server.' . PHP_EOL);
}

// give it a second to start
usleep(200000);

if (!posix_kill($pid, 0)) {
    exit('Error: Failed to start PHP Build-in server.' . PHP_EOL);
}

passthru($php . ' ./RunUnitTests.php ' . implode(' ', array_slice($argv, 1)));

posix_kill($pid, SIGHUP);
pcntl_waitpid($pid, $status, WNOHANG);
