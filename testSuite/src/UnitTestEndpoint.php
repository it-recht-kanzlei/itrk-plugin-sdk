<?php

namespace PluginSDKTestSuite;

require_once __DIR__ . '/../../sdk/require_all.php';

class MyLTIHandler extends \ITRechtKanzlei\LTIHandler {
    public function isTokenValid(string $token): bool {
        return $token == 'TEST_TOKEN';
    }

    /**
     * @throws \Exception
     */
    public function handleActionPush(\ITRechtKanzlei\LTIPushData $data): \ITRechtKanzlei\LTIPushResult {
        if ($data->getType() != 'impressum' && $data->hasPdf()) {
            $data->getPdf();
        }
        // This header is only for the local test server and not an official header.
        if (isset($_SERVER['HTTP_X_ITRKTEST_MULTISHOP'])) {
            $data->getMultiShopId();
        }
        return new \ITRechtKanzlei\LTIPushResult('https://example.org/'.$data->getType());
    }

    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        $accountList = new \ITRechtKanzlei\LTIAccountListResult();
        $accountList->addAccount('1', 'example store name 1', ['de', 'en', 'fr']);
        $accountList->addAccount('2', 'example store name 2', ['de_DE']);
        $accountList->addAccount('3', 'example store name 3');
        return $accountList;
    }
}

error_reporting(-1);
ini_set('display_errors', true);
ini_set('html_errors', false);

$ltiHandler = new MyLTIHandler();
$lti = new \ITRechtKanzlei\LTI($ltiHandler, '1.2', '1.0');
$responseResult = $lti->handleRequest($_POST['xml'] ?? '');

header('Content-Type: application/xml; charset=utf-8');
header('Content-Length: ' . strlen($responseResult));
echo $responseResult;
