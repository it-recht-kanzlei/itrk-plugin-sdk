<?php

namespace PluginSDKTestSuite;
include_once __DIR__ . '/ColorCodes.php';

class UnitTest {
    protected $userAccountId = null;
    private $apiUrl = null;
    private $apiToken = null;
    private $multishop = null;
    private $testsStatus = true;

    public function __construct($apiUrl, $apiToken, $multishop, $userAccountId=null) {
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
        $this->multishop = $multishop;
        $this->userAccountId = $userAccountId;
    }

    public function runTest($testName): void {
        $fileHandle = fopen($testName, "r") or die("Unable to open file!");
        $jsonContent = json_decode(fread($fileHandle, filesize($testName)));
        fclose($fileHandle);

        if (isset($jsonContent->multishop) && $jsonContent->multishop != $this->multishop) {
            return;
        }

        if ($this->apiToken) {
            $jsonContent->data = str_replace('<user_auth_token>TEST_TOKEN</user_auth_token>', '<user_auth_token>' . $this->apiToken . '</user_auth_token>', $jsonContent->data);
        }

        if ($this->userAccountId) {
            $jsonContent->data = str_replace('<user_account_id>123</user_account_id>', '<user_account_id>' . $this->userAccountId . '</user_account_id>', $jsonContent->data);
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["xml" => $jsonContent->data]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
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
                ($jsonContent->result->status == $xmlResponse->status)
                && (
                    ($jsonContent->result->status == 'error' && $jsonContent->result->error == $xmlResponse->error)
                    || ($jsonContent->result->status != 'error')
                )
            ) {
                $msg = sprintf("Test '%s' successful!", $jsonContent->name);
                self::writeWithColor(ColorCodes::GREEN, $msg);
            } else {
                $msg = sprintf("Test '%s' failed!", $jsonContent->name);
                self::writeWithColor(ColorCodes::RED, $msg);
                $this->testsStatus = false;

                self::writeWithColor(ColorCodes::YELLOW, sprintf("--- Data posted (to %s) ---", $this->apiUrl));
                $template = simplexml_load_string($jsonContent->data);
                $dom = dom_import_simplexml($template)->ownerDocument;
                $dom->formatOutput = true;
                self::writeWithColor(ColorCodes::GRAY, trim($dom->saveXML()));

                self::writeWithColor(ColorCodes::YELLOW, "--- Response ---");
                $dom = dom_import_simplexml($xmlResponse)->ownerDocument;
                $dom->formatOutput = true;
                self::writeWithColor(ColorCodes::RESET, $dom->saveXML());
            }
        } else {
            $msg = sprintf("Parsing response of test '%s' failed!", $jsonContent->name);
            self::writeWithColor(ColorCodes::YELLOW, $msg);
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