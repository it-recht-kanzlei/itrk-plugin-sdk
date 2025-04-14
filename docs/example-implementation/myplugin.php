<?php
/**
 * This is an example on how to use the new IT-Recht Kanzlei plugin SDK.
 *
 * This SDK is made to be used for developing own plugins in connection with the version/getaccountlist/push calls of our system.
 * The SDK is kept intentionally simplistic. You have to provide your own implementation of the ITRechtKanzlei\LTIHandler class.
 * To do so at the minimum only two methods have to be implemented by you:
 *  - public function isTokenValid(string $token): bool;
 *  - public function handleActionPush(ITRechtKanzlei\LTIPushData $data): ITRechtKanzlei\LTIPushResult;
 * More detailed information on the methods can be found in the class ITRechtKanzlei\LTIHandler.
 *
 * The SDK can be used in just 2 steps (see example below):
 *  1. Override all abstract ITRechtKanzlei\LTIHandler methods
 *  2. Create object of ITRechtKanzlei\LTI and call public function handleRequest($xml) method like seen in the example below
 *
 * There is no need to create the resulting xml response yourself.
 * All these mechanisms are automatically taken over by the LTI class.
 *
 * Please do NOT edit any classes within the \ITRechtKanzlei\ Namespace to ensure that the code remains executable.
 *
 * The example below shows you how to use the SDK properly.
 */
require_once __DIR__ . '/../../sdk/require_all.php';
require_once __DIR__ . '/MyLTIHandler.php';

// 1. Instantiate object of class that overrides abstract methods
$ltiHandler = new MyLTIHandler();

// 2. Instantiate of ITRechtKanzlei\LTI and call handleRequest(...) method
$lti = new \ITRechtKanzlei\LTI($ltiHandler, '1.2', '1.0');

// Include a stack trace in the error result. Only used for debugging purposes.
// Should be disabled for the production version of your plugin.
$lti->setIncludeErrorStackTrace(true);

// 3. Handle the request
if ((strpos($_SERVER['CONTENT_TYPE'], 'x-www-form-urlencoded') !== false)
    && isset($_POST['xml'])
) {
    // This is the legavy way of receiving the xml document.
    $xml = $_POST['xml'];
} else {
    // This is the preferred method of receiving the xml document.
    $xml = file_get_contents('php://input');
}
$responseResult = $lti->handleRequest($xml);

header('Content-Type: application/xml; charset=utf-8');
header('Content-Length: ' . strlen($responseResult));
echo $responseResult;

// This should be the end of your plugin code
