<?php

class LTIVersionResult {
    private $shopVersion = null;

    public function __construct($shopVersion) {
        $this->shopVersion = $shopVersion;
    }

    public function getShopVersion(): string {
        return $this->shopVersion;
    }
}

?>