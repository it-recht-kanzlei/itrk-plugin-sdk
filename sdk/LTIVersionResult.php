<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

use SimpleXMLElement;

/**
 * The response class for the GetVersion call.
 *
 * This class can collect some additional information about its environment that
 * can be used for debugging and troubleshooting issues.
 */
class LTIVersionResult extends \ITRechtKanzlei\LTIResult {
    private $systemPlugins = [];
    private $includeApacheModules = false;

    protected function buildXML(): SimpleXMLElement {
        $simpleXml = parent::buildXML();

        if ($this->includeApacheModules && function_exists('apache_get_modules')) {
            $modules = $simpleXml->addChild('meta_apache_modules');
            foreach (apache_get_modules() as $module) {
                $modules->addChild('module', $module);
            }
        }
        if (!empty($this->systemPlugins)) {
            $plugins = $simpleXml->addChild('meta_system_plugins');
            foreach ($this->systemPlugins as $plugin) {
                $this->buildNode($plugins, 'plugin', $plugin);
            }
        }
        return $simpleXml;
    }

    /**
     * If enabled the list of apache modules will be included in the response.
     * This helps the support of IT-Recht Kanzlei to troubleshoot
     * problematic interactions between the modules and this plugin.
     *
     * @param bool $include
     * @return self
     */
    public function includeApacheModules(bool $include): self {
        $this->includeApacheModules = $include;
        return $this;
    }

    /**
     * Adds a list of third party plugins to the response to help with
     * troubleshooting problematic interactions between those plugins.
     *
     * @param string $pluginName
     * @param string $version
     * @return self
     */
    public function addPluginInfo(string $pluginName, string $version): self {
        $this->systemPlugins[] = [
            'name'    => $pluginName,
            'version' => $version,
        ];
        return $this;
    }
}
