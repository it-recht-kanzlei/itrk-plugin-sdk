<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */
namespace ITRechtKanzlei;

class LTI {
    public const SDK_VERSION = '1.2.9';

    private $ltiHandler;
    private $shopVersion;
    private $modulVersion;
    private $errorCallback = null;
    private $includeErrorStackTrace = false;

    /**
     * @param LTIHandler $ltiHandler Your implementation of the LTIHandler
     * @param string $shopVersion
     * @param string $modulVersion
     */
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

    /**
     * You can define a callback that is executed if an exception is thrown during the
     * processing of the XML request.
     * This allows you to log unexpected errors, for example. However, the exception
     * cannot be suppressed or replaced.
     *
     * @param callable|null $errorCallback
     * @return $this
     */
    public function setErrorCallback(?callable $errorCallback): self {
        $this->errorCallback = $errorCallback;
        return $this;
    }

    /**
     * Enable this option to include a stack trace in the error result.
     * Mainly used for debugging purposes.
     *
     * @param bool $includeErrorStackTrace
     * @return $this
     */
    public function setIncludeErrorStackTrace(bool $includeErrorStackTrace): self {
        $this->includeErrorStackTrace = $includeErrorStackTrace;
        return $this;
    }

    /**
     * Processes the received XML file. This function will call the corresponding handler
     * methods of your LTIHandler class. Use the __toString() method of LTIResult to generate
     * the expected XML response.
     *
     * @param string|null $xml
     * @return LTIResult
     */
    public function handleRequest(?string $xml): LTIResult {
        try {
            $this->ltiHandler->preHandleRequest();

            libxml_use_internal_errors(true);

            if (!is_string($xml) || empty($xml = trim($xml))) {
                throw new LTIError('No XML data provided.', LTIError::PARSING_ERROR);
            }

            if (!function_exists('simplexml_load_string')) {
                throw new LTIError('Extension SimpleXML not available on host system.', LTIError::PARSING_ERROR);
            }
            $xmlData = simplexml_load_string($xml);

            if (!$xmlData) {
                throw new LTIError('Error parsing xml value.', LTIError::PARSING_ERROR);
            }

            $apiVersion = isset($xmlData->api_version) ? (string)$xmlData->api_version : '';
            if (empty($apiVersion)) {
                throw new LTIError('No API version has been provided.', LTIError::INVALID_API_VERSION);
            }
            if (!version_compare($apiVersion, '1.0', '>=')
                || !version_compare($apiVersion, '2-dev', '<')
            ) {
                throw new LTIError('The API version is not supported.', LTIError::INVALID_API_VERSION);
            }

            if (isset($xmlData->user_auth_token) && strval($xmlData->user_auth_token)) {
                // validate token
                if (!$this->ltiHandler->isTokenValid($xmlData->user_auth_token)) {
                    throw new LTIError('Invalid authentication token.', LTIError::INVALID_AUTH_TOKEN);
                }
            } elseif (
                isset($xmlData->user_username) && strval($xmlData->user_username)
                && isset($xmlData->user_password) && strval($xmlData->user_password)
            ) {
                // validate user/pass
                if (!$this->ltiHandler->validateUserPass($xmlData->user_username, $xmlData->user_password)) {
                    throw new LTIError('Invalid username and password.', LTIError::INVALID_AUTH_TOKEN);
                }
            } else {
                throw new LTIError('Missing authentication credentials.', LTIError::INVALID_AUTH_TOKEN);
            }

            $action = isset($xmlData->action) ? strtolower((string)$xmlData->action) : '';
            if (empty($action)) {
                throw new LTIError('Missing action.', LTIError::INVALID_ACTION);
            }

            switch ($action) {
                case 'push':
                    $data = new \ITRechtKanzlei\LTIPushData($xmlData);
                    if (($data->getCountry() === 'XX') && ($data->getLanguageIso639_1() === 'xx')) {
                        // Legacy credentials verification.
                        throw new LTIError('Credentials OK', LTIError::VALID_AUTH_TOKEN);
                    }
                    $ltiResult = $this->ltiHandler->handleActionPush($data);
                    break;
                case 'getaccountlist':
                    $ltiResult = $this->ltiHandler->handleActionGetAccountList();
                    break;
                case 'getversion':
                    $ltiResult = $this->ltiHandler->handleActionGetVersion();
                    break;
                default:
                    throw new LTIError('Invalid action sent: ' . $action, LTIError::INVALID_ACTION);
            }

            $ltiResult->setVersions($this->shopVersion, $this->modulVersion);
            return $ltiResult;

        } catch (\Throwable $e) {
            if (is_callable($this->errorCallback)) {
                call_user_func($this->errorCallback, $e);
            }
            $error = new \ITRechtKanzlei\LTIErrorResult($e, $this->includeErrorStackTrace);
            $error->setVersions($this->shopVersion, $this->modulVersion);
            return $error;
        }
    }

    /**
     * Helper method for generating a token. The token is used to authenticate
     * the IT Recht Kanzlei Push Service to your system.
     * Once the token has been generated, it should be saved in your system and
     * displayed to the client. The client then inserts the token when setting
     * up the interface in the client portal.
     * @see LTIHandler::isTokenValid()
     *
     * @param int $length The length of the token
     * @param string|null $alphabet A list of characters the token is composed of.
     * @return string A token
     */
    public static function generateToken(int $length = 32, ?string $alphabet = null): string {
        if (!$alphabet) {
            $alphabet = implode(range('a', 'z')).implode(range('A', 'Z')).implode(range(0, 9));
        }
        $alphabetLength = strlen($alphabet) - 1;
        $token = '';
        for ($i = 0; $i < $length; ++$i) {
            $token .= substr($alphabet, random_int(0, $alphabetLength), 1);
        }
        return $token;
    }
}
