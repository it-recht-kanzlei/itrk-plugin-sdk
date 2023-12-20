<?php

namespace PluginSDKTestSuite;

require_once __DIR__ . '/ColorCodes.php';

class UnitTest {
    const LOCAL_TEST_SERVER = 'http://localhost:7080/UnitTestEndpoint.php';

    private $apiUrl = null;
    private $apiToken = null;
    private $userAccountId = null;
    private $verbose = false;

    private $testsStatus = true;

    private $localServerPid = null;

    public function __construct(string $apiUrl, ?string $apiToken, ?string $userAccountId = null, bool $verbose = false) {
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
        $this->userAccountId = $userAccountId;
        $this->verbose = $verbose;

        if ($this->apiUrl === self::LOCAL_TEST_SERVER) {
            $this->startLocalServer();
        }
    }

    private function startLocalServer() {
        $php = $_SERVER['_'];
        // Starting PHP Build-in server in background
        $this->localServerPid = exec(sprintf("%s -q -S 0.0.0.0:7080 %s/UnitTestEndpoint.php > /dev/null 2>&1 & echo $!", $php, __DIR__));
        if (!$this->localServerPid) {
            self::writeWithColor(ColorCodes::RED, 'Error: Failed to start PHP Build-in server.');
            exit(-1);
        }

        // give it a second to start
        usleep(200000);

        if (!posix_kill($this->localServerPid, 0)) {
            self::writeWithColor(ColorCodes::RED, 'Error: Failed to start PHP Build-in server.');
            exit(-1);
        }
    }

    public function __destruct() {
        if ($this->localServerPid === null) {
            return;
        }
        $status = null;
        posix_kill($this->localServerPid, SIGHUP);
        pcntl_waitpid($this->localServerPid, $status, WNOHANG);
    }

    public function runTest(string $testFileName): void {
        if (!is_readable($testFileName)
            || !($jsonContent = json_decode(file_get_contents($testFileName), true))
            || empty($jsonContent)
        ) {
            throw new \RuntimeException(sprintf('Unable to read test file %s.', $testFileName));
        }
        $testName = sprintf('"%s" (%s)', preg_replace('/\.json$/', '$1', basename($testFileName)), $jsonContent['name']);

        if ($this->apiToken) {
            $jsonContent['data'] = str_replace(
                '<user_auth_token>TEST_TOKEN</user_auth_token>',
                '<user_auth_token>' . $this->apiToken . '</user_auth_token>',
                $jsonContent['data']
            );
        }

        $addHeaders = [];
        if ($this->userAccountId) {
            $jsonContent['data']= str_replace(
                '<user_account_id>123</user_account_id>',
                '<user_account_id>' . $this->userAccountId . '</user_account_id>',
                $jsonContent['data']
            );
        } elseif (isset($jsonContent['multishop']) && $jsonContent['multishop']) {
            if ($this->localServerPid === null) {
                // No nultishop system, otherwise a user account id would have been provided.
                $msg = sprintf(
                    "Test %s skipped. Provide a user-account-id if your system supports multiple sales channels.",
                    $testName
                );
                self::writeWithColor(ColorCodes::YELLOW, $msg);
                return;
            }
            // Only for the local test server. This is not an official header.
            $addHeaders[] = 'X-ITRKTEST-MULTISHOP: true';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["xml" => $jsonContent['data']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $addHeaders);

        $response = curl_exec($ch);
        if ($response === false) {
            self::writeWithColor(ColorCodes::RED, 'cURL error: ' . curl_error($ch));
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        libxml_use_internal_errors(true);
        $xmlResponse = simplexml_load_string($body);

        if ($xmlResponse) {
            if (
                ($jsonContent['result']['status'] == $xmlResponse->status)
                && (
                    ($jsonContent['result']['status'] == 'error' && $jsonContent['result']['error'] == $xmlResponse->error)
                    || ($jsonContent['result']['status'] != 'error')
                )
            ) {
                $msg = sprintf("Test %s successful!", $testName);
                self::writeWithColor(ColorCodes::GREEN, $msg);
            } else {
                $msg = sprintf("Test %s failed!", $testName);
                self::writeWithColor(ColorCodes::RED, $msg);
                $this->testsStatus = false;

                self::writeWithColor(ColorCodes::YELLOW, sprintf("--- Data posted (to %s) ---", $this->apiUrl));
                $template = simplexml_load_string($jsonContent['data']);
                $dom = dom_import_simplexml($template)->ownerDocument;
                $dom->formatOutput = true;
                self::writeWithColor(ColorCodes::GRAY, trim($dom->saveXML()));

                self::writeWithColor(ColorCodes::YELLOW, "--- Response ---");
                $dom = dom_import_simplexml($xmlResponse)->ownerDocument;
                $dom->formatOutput = true;
                self::writeWithColor(ColorCodes::RESET, $dom->saveXML());
            }
        } else {
            $msg = sprintf("Parsing response of test %s failed!", $testName);
            self::writeWithColor(ColorCodes::YELLOW, $msg);
            foreach (libxml_get_errors() as $error) {
                self::writeWithColor(ColorCodes::RESET, sprintf('    XML Error on line %d: %s', (int)$error->line, trim($error->message)));
            }
            libxml_clear_errors();
            if ($this->verbose) {
                self::writeWithColor(ColorCodes::RESET, sprintf("    Got the following response:\n-------------------\n%s\n-------------------\n", $body));
            }
            $this->testsStatus = false;
        }

        curl_close($ch);
    }

    public function getTestsStatus(): bool {
        return $this->testsStatus;
    }

    public static function writeWithColor($color, $text): void {
        echo sprintf("%s%s\n", $color, $text);
        echo ColorCodes::RESET;
    }

}
