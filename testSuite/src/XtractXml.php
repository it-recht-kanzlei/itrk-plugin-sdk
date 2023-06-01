<?php

if (!isset($argv[2])) {
    fwrite(STDERR, 'usage: XtractXml.php test-filename YourApiToken [user_account_id]' . PHP_EOL . PHP_EOL);
    die();
}

$filename = basename($argv[1]);
if (substr($filename, -5) !== '.json') {
    $filename .= '.json';
}
$token = $argv[2];

$json = json_decode(file_get_contents(__DIR__ . '/../testCases/' . $filename), true);
$xml = $json['data'];
$xml = str_replace('<user_auth_token>TEST_TOKEN</user_auth_token>', '<user_auth_token>' . $token . '</user_auth_token>', $xml);

if (isset($argv[3])) {
    $user_account_id = $argv[3];
    $xml = str_replace('<user_account_id>123</user_account_id>', '<user_account_id>' . $user_account_id . '</user_account_id>', $xml);
}

echo $xml;