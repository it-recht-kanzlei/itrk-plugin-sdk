<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */
namespace ITRechtKanzlei;

use Exception;

require_once __DIR__ . '/LTIError.php';
require_once __DIR__ . '/LTIHandler.php';
require_once __DIR__ . '/LTIResult.php';

class LTI {
    const SDK_VERSION = '1.2.1';

    private $ltiHandler;
    private $shopVersion;
    private $modulVersion;
    private $xmlData;
    private $isMultiShop;

    public function __construct(\ITRechtKanzlei\LTIHandler $ltiHandler, string $shopVersion, string $modulVersion, bool $isMultiShop) {
        $this->ltiHandler = $ltiHandler;
        $this->shopVersion = $shopVersion;
        $this->modulVersion = $modulVersion;
        $this->isMultiShop = $isMultiShop;
    }

    /**
     * @return string
     */
    public function getSdkVersion(): string {
        return self::SDK_VERSION;
    }

    public function handleRequest(?string $xml): LTIResult {
        try {
            libxml_use_internal_errors(true);

            if (!is_string($xml) || empty($xml = trim($xml))) {
                throw new \InvalidArgumentException('No XML data provided.', LTIError::PARSING_ERROR);
            }

            if (!function_exists('simplexml_load_string')) {
                throw new \Exception('Extension SimpleXML not available on host system', LTIError::PARSING_ERROR);
            }
            $this->xmlData = simplexml_load_string($xml);

            if (!$this->xmlData) {
                throw new \Exception('Error parsing xml value!', LTIError::PARSING_ERROR);
            }

            $this->checkXmlElementAvailable('api_version', LTIError::INVALID_API_VERSION);
            $this->checkXmlElementAvailable('user_auth_token');

            if (!$this->ltiHandler->isTokenValid($this->xmlData->user_auth_token)) {
                throw new \Exception('Invalid user auth token!', LTIError::INVALID_AUTH_TOKEN);
            }

            $this->checkXmlElementAvailable('action', LTIError::INVALID_ACTION);
            $ltiResult = null;

            switch ($this->xmlData->action) {
                case 'push':
                    if (!$this->isMultiShop) {
                        require_once __DIR__ . '/LTIPushData.php';
                        $ltiResult = $this->ltiHandler->handleActionPush(
                            new \ITRechtKanzlei\LTIPushData($this->xmlData)
                        );
                    } else {
                        require_once __DIR__ . '/LTIMultiShopPushData.php';
                        $ltiResult = $this->ltiHandler->handleActionPush(
                            new \ITRechtKanzlei\LTIMultiShopPushData($this->xmlData)
                        );
                    }
                    break;
                case 'getaccountlist':
                    if ($this->isMultiShop) {
                        $ltiResult = $this->ltiHandler->handleActionGetAccountList();
                    } else {
                        throw new \Exception('Shop is not a multishop system!', LTIError::NO_MULTISHOP_ERROR);
                    }
                    break;
                case 'getversion':
                    $ltiResult = $this->ltiHandler->handleActionGetVersion();
                    break;
                default:
                    throw new \Exception('Wrong action sent: ' . $this->xmlData->action, LTIError::INVALID_ACTION);
            }

            $ltiResult->setVersions($this->shopVersion, $this->modulVersion);
            $this->ltiHandler->sendResponse($ltiResult);
            return $ltiResult;

        } catch (\Exception $e) {
            require_once __DIR__ . '/LTIErrorResult.php';
            $error = new \ITRechtKanzlei\LTIErrorResult($e);
            $error->setVersions($this->shopVersion, $this->modulVersion);
            $this->ltiHandler->sendResponse($error);
            return $error;
        }
    }

    private function checkXmlElementAvailable(string $name, int $errorCode = LTIError::UNKNOWN_ERROR) {
        try {
            $value = $this->xmlData->$name;

            if (empty($value)) {
                throw new \Exception('XML element '. $name . '\'s value is empty!', $errorCode);
            }

        } catch (Exception $e) {
            throw new \Exception('XML element '. $name . ' not set!', $errorCode);
        }
    }
}
