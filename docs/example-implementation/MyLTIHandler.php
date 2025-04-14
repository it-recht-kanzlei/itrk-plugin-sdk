<?php

/**
 * This is an example class which could represent the interface between
 * your system and the SDK.
 */
class MyLTIHandler extends \ITRechtKanzlei\LTIHandler {

    /**
     * You can inject any dependencies that are required for your system here.
     */
    public function __construct() {

    }

    /**
     * This method can be used to initialize resources or to validate preconditions
     * that the target system has to fulfill in order to operate.
     * If the necessary conditions are not met, you can throw an exception here
     * which will be converted to a properly formatted error response.
     * @throws \Exception
     */
    public function preHandleRequest(): void {

    }

    public function isTokenValid(string $token): bool {
        // Validate the token here.
        // The token is generated once and stored in your system.
        // For more details see the readme.md and LTI::generateToken().
        return $token == '12345678';
    }

    private function getPluginList(): array {
        // Used for the handleActionGetVersion() example. Do not copy if not needed!
        return [
            'plugin-image-enhancer' => '2.4.2',
            'plugin-multilingual'   => '5.23',
            'plugin-herpderp'       => '9001.3.1415',
        ];
    }

    public function handleActionGetVersion(): \ITRechtKanzlei\LTIVersionResult {
        $result = new \ITRechtKanzlei\LTIVersionResult();
        // If you do not want to share any additional data with the client portal of
        // IT-Recht Kanzlei, you can simply return the result here with
        // return $result;
        // or use the implementation of the parent class.
        //
        // If you want to share additional data you can checkout the following examples:
        //
        // Includes the list of installed apache2 modules if php is running as an
        // apache2 module. This helps the support of IT-Recht Kanzlei to troubleshoot
        // problematic interactions between the modules and this plugin.
        $result->includeApacheModules(true);

        // Get a list of all available plugins that might interfere with this plugin.
        // This helps with troubleshooting problematic interactions between those plugins.
        $plugins = $this->getPluginList();
        foreach ($plugins as $plugin => $version) {
            $result->addPluginInfo($plugin, $version);
        }

        return $result;
    }


    /**
     * @throws Exception
     */
    public function handleActionPush(\ITRechtKanzlei\LTIPushData $data): \ITRechtKanzlei\LTIPushResult {
        // Implement the logic to store your pushed document to the shop here and return an object of ITRechtKanzlei\LTIPushResult with
        // an url where to find the currently uploaded document. Replace the url with your document url.

        // Basic implementation for this example:
        $filepath = __DIR__.'/storage/';
        $filename = sprintf(
            '%s_%s_%s',
            $data->getType(),
            $data->getLanguageIso639_1(),
            $data->getCountry()
        );

        // If your system supports multiple sales channels you can resolve your sales channel here
        // using the method $data->getMultiShopId().

        //$filepath .= $data->getMultiShopId().'_';

        file_put_contents($filepath.$filename.'.txt', $data->getText());
        file_put_contents($filepath.$filename.'.html', $data->getTextHtml());

        if ($data->hasPdf()) {
            $filename = $data->getLocalizedFileName();
            $pdf_binary = $data->getPdf();
            file_put_contents($filepath.$filename.'.pdf', $pdf_binary);
        }

        return new \ITRechtKanzlei\LTIPushResult('https://www.example.com/policies/legal-notice');
    }

    // This method is primarily intended for multi-shop systems, but should also be implemented for other systems
    // in order to inform the IT-Recht Kanzlei's client portal about the languages and
    // sales countries available in your system.
    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        // add all your shops here to the $accountList like seen in the example.
        $accountList = new \ITRechtKanzlei\LTIAccountListResult();
        $accountList->addAccount('3', 'example store name 1', ['de_DE', 'en_GB'], ['DE', 'AT', 'GB']);
        $accountList->addAccount('8', 'example store name 2', ['de', 'fr', 'it'], ['CH']);
        $accountList->addAccount('122', 'example store name 3', ['de'], ['DE']);

        return $accountList;
    }
}
