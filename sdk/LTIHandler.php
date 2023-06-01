<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

require_once __DIR__ . '/LTIPushData.php';
require_once __DIR__ . '/LTIResult.php';
require_once __DIR__ . '/LTIAccountListResult.php';
require_once __DIR__ . '/LTIPushResult.php';
require_once __DIR__ . '/LTIVersionResult.php';

abstract class LTIHandler {
    public function sendResponse(string $responseResult): void {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Length: ' . strlen($responseResult));
        echo $responseResult;
    }

    /**
     * This method can be overwritten by you if you wish to extend the response
     * with useful debugging information. See LTIVersionResult for more details.
     */
    public function handleActionGetVersion(): \ITRechtKanzlei\LTIVersionResult {
        return new \ITRechtKanzlei\LTIVersionResult();
    }

    /**
     * This method must be overwritten by you. Please add a check mechanism to check whether the sent token is valid or not.
     * Singleshop systems might implement the check like it is made in the example.php file.
     * Multishop systems maybe need database select to check the token.
     */
    public abstract function isTokenValid(string $token): bool;

    /**
     * This method must be overwritten by you. Please add the logic to push the received file to your shop system.
     */
    public abstract function handleActionPush(LTIPushData $data): \ITRechtKanzlei\LTIPushResult;

    /**
     * This method must be overwritten by you if your system is a multishop system.
     *
     * Please add all our shops to the shoplist like in example.php.
     * This method is only used when ITRechtKanzlei/LTI->isMultiShop is true.
     */
    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        throw new \RuntimeException('Please implement this method in %s.', get_class($this));
    }

}
