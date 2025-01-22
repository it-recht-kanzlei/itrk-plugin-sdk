<?php

namespace PluginSDKTestSuite;

abstract class UnitTest {
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    abstract public function getTestName(): string;
    abstract public function getRequestXml(): string;
    abstract public function isMultiShopTest(): bool;
    abstract public function getResult(): array;

    protected function expandHttpHeaders(array $headers): array {
        return $headers;
    }

    /**
     * @param \CurlHandle|resource $handle
     * @return \CurlHandle|resource
     */
    protected function expandCurlHandle($handle) {
        return $handle;
    }

    public static function formatXmlStr($xml): string {
        if ($xml instanceof \SimpleXMLElement) {
            $xml = $xml->asXML();
        }
        try {
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            if (!$dom->loadXML($xml)) {
                return $xml;
            }
            return $dom->saveXML();
        } catch (\DOMException $e) {
            return $xml;
        }
    }

    public function prepareXml(): string {
        $xml = $this->getRequestXml();
        if (!empty($this->options->token)) {
            $xml = str_replace(
                '<user_auth_token>TEST_TOKEN</user_auth_token>',
                '<user_auth_token>' . htmlspecialchars($this->options->token) . '</user_auth_token>',
                $xml
            );
        }
        if (!empty($this->options->userAccountId)) {
            $xml = str_replace(
                '<user_account_id>123</user_account_id>',
                '<user_account_id>' . htmlspecialchars($this->options->userAccountId) . '</user_account_id>',
                $xml
            );
        }
        return $xml;
    }

    public final function runTest(): bool {
        $reflector = new \ReflectionClass($this);

        $testName = sprintf('"%s" (%s)', basename($reflector->getFileName(), '.php'), $this->getTestName());
        $xml = $this->prepareXml();

        $addHeaders = [];
        if (empty($this->options->userAccountId) && $this->isMultiShopTest()) {
            if ($this->options->url === TestRunner::LOCAL_TEST_SERVER) {
                // No multi-shop system, otherwise a user account id would have been provided.
                $msg = sprintf(
                    "Test %s skipped. Provide a user-account-id if your system supports multiple sales channels.",
                    $testName
                );
                ConsoleColor::writeWithColor(ConsoleColor::YELLOW, $msg);
                return true;
            }
            // Only for the local test server. This is not an official header.
            $addHeaders[] = 'X-ITRKTEST-MULTISHOP: true';
        }

        if ($this->options->postForm) {
            $post = ['xml' => $xml];
        } else {
            $post = $xml;
            $addHeaders[] = 'content-type: text/xml;charset=utf-8';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->options->url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->expandHttpHeaders($addHeaders));

        $ch = $this->expandCurlHandle($ch);

        $response = curl_exec($ch);
        if ($response === false) {
            ConsoleColor::writeWithColor(ConsoleColor::RED, 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        libxml_use_internal_errors(true);
        $xmlResponse = simplexml_load_string($body);

        $expectedResult = $this->getResult();

        $xlmResponseShown = false;
        $status = false;

        if ($xmlResponse) {
            if (
                ($expectedResult['status'] == $xmlResponse->status)
                && (
                    (($expectedResult['status'] == 'error') && ($expectedResult['error'] == $xmlResponse->error))
                    || ($expectedResult['status'] != 'error')
                )
            ) {
                $msg = sprintf("Test %s successful!", $testName);
                ConsoleColor::writeWithColor(ConsoleColor::GREEN, $msg);
                $status = true;
            } else {
                $msg = sprintf("Test %s failed!", $testName);
                ConsoleColor::writeWithColor(ConsoleColor::RED, $msg);

                ConsoleColor::writeWithColor(ConsoleColor::YELLOW, sprintf("--- Request data posted (to %s) ---", $this->options->url));
                ConsoleColor::writeWithColor(ConsoleColor::GRAY, self::formatXmlStr($xml));
                ConsoleColor::writeWithColor(ConsoleColor::YELLOW, "--- Response ---");
                ConsoleColor::writeWithColor(ConsoleColor::RESET, self::formatXmlStr($xmlResponse));
                $xlmResponseShown = true;
            }
        } else {
            $msg = sprintf("Parsing response of test %s failed!", $testName);
            ConsoleColor::writeWithColor(ConsoleColor::YELLOW, $msg);
            foreach (libxml_get_errors() as $error) {
                ConsoleColor::writeWithColor(ConsoleColor::RESET, sprintf('    XML Error on line %d: %s', (int)$error->line, trim($error->message)));
            }
            libxml_clear_errors();
        }
        if ($this->options->verbose && !$xlmResponseShown) {
            ConsoleColor::writeWithColor(ConsoleColor::RESET, sprintf("Got the following response:\n-------------------\n%s\n-------------------\n", $body));
        }

        return $status;
    }

}
