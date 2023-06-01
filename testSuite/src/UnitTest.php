<?php
namespace PluginSDKTestSuite;
include_once __DIR__ . '/ColorCodes.php';

class UnitTest {
    private $apiUrl = null;
    private $apiToken = null;
    private $multishop = null;
    private $testsStatus = true;

    public function __construct($apiUrl, $apiToken, $multishop) {
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
        $this->multishop = $multishop;
    }

    public function runTest($testName) {
        $fileHandle = fopen($testName, "r") or die("Unable to open file!");
        $jsonContent = json_decode(fread($fileHandle,filesize($testName)));
        fclose($fileHandle);

        if (isset($jsonContent->multishop) && $jsonContent->multishop != $this->multishop) {
            return;
        }

        if ($this->apiToken) {
            $jsonContent->data = str_replace('TEST_TOKEN', $this->apiToken, $jsonContent->data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [ "xml" => $jsonContent->data ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
        $response = curl_exec($ch);

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
                $msg = sprintf("Test '%s' successfull!", $jsonContent->name);
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

    public function getTestsStatus()  {
        return $this->testsStatus;
    }

    public static function writeWithColor($color, $text) {
        echo sprintf("%s %s \n", $color, $text);
        echo ColorCodes::RESET;
    }

};