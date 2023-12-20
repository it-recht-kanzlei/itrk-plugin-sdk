<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use Exception;

class LTI {
    const SDK_VERSION = '1.2.5';

    private $ltiHandler;
    private $shopVersion;
    private $modulVersion;
    private $xmlData;

    private $errorCallback = null;

    public function __construct(\ITRechtKanzlei\LTIHandler $ltiHandler, string $shopVersion, string $modulVersion) {
        $this->ltiHandler = $ltiHandler;
        $this->shopVersion = $shopVersion;
        $this->modulVersion = $modulVersion;
    }

    /**
     * @return string
     */
    public function getSdkVersion(): string {
        return self::SDK_VERSION;
    }

    public function setErrorCallback(?callable $errorCallback): self {
        $this->errorCallback = $errorCallback;
        return $this;
    }

    public function handleRequest(?string $xml): LTIResult {
        try {
            libxml_use_internal_errors(true);

            if (!is_string($xml) || empty($xml = trim($xml))) {
                throw new LTIError('No XML data provided.', LTIError::PARSING_ERROR);
            }

            if (!function_exists('simplexml_load_string')) {
                throw new LTIError('Extension SimpleXML not available on host system.', LTIError::PARSING_ERROR);
            }
            $this->xmlData = simplexml_load_string($xml);

            if (!$this->xmlData) {
                throw new LTIError('Error parsing xml value.', LTIError::PARSING_ERROR);
            }

            $this->checkXmlElementAvailable('api_version', LTIError::INVALID_API_VERSION);

            if (isset($this->xmlData->user_auth_token) && strval($this->xmlData->user_auth_token)) {
                // validate token
                if (!$this->ltiHandler->isTokenValid($this->xmlData->user_auth_token)) {
                    throw new LTIError('Invalid user auth token.', LTIError::INVALID_AUTH_TOKEN);
                }
            } else {
                // validate user/pass
                if (!$this->ltiHandler->validateUserPass($this->xmlData->user_username, $this->xmlData->user_password)) {
                    throw new LTIError('Invalid user/pass.', LTIError::INVALID_AUTH_TOKEN);
                }
            }

            $this->checkXmlElementAvailable('action', LTIError::INVALID_ACTION);
            $ltiResult = null;

            switch ($this->xmlData->action) {
                case 'push':
                    $ltiResult = $this->ltiHandler->handleActionPush(
                        new \ITRechtKanzlei\LTIPushData($this->xmlData)
                    );
                    break;
                case 'getaccountlist':
                    $ltiResult = $this->ltiHandler->handleActionGetAccountList();
                    break;
                case 'getversion':
                    $ltiResult = $this->ltiHandler->handleActionGetVersion();
                    break;
                default:
                    throw new LTIError('Invalid action sent: ' . $this->xmlData->action, LTIError::INVALID_ACTION);
            }

            $ltiResult->setVersions($this->shopVersion, $this->modulVersion);
            return $ltiResult;

        } catch (\Throwable $e) {
            if (is_callable($this->errorCallback)) {
                call_user_func($this->errorCallback, $e);
            }
            $error = new \ITRechtKanzlei\LTIErrorResult($e);
            $error->setVersions($this->shopVersion, $this->modulVersion);
            return $error;
        }
    }

    private function checkXmlElementAvailable(string $name, int $errorCode): void {
        if (!isset($this->xmlData->$name)) {
            throw new LTIError('XML element ' . $name . ' not set.', $errorCode);
        }
        $value = $this->xmlData->$name;
        if (empty($value)) {
            throw new LTIError('XML element ' . $name . '\'s value is empty.', $errorCode);
        }
    }
}
