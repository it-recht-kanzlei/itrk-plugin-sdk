<?php

namespace PluginSDKTestSuite;

class ConsoleColor {
    const RESET = "\e[0m";
    const RED = "\e[31m";
    const GREEN = "\e[32m";
    const WHITE = "\e[97m";
    const GRAY = "\e[37;1m";
    const YELLOW = "\e[33m";

    public static function writeWithColor($color, $text): void {
        echo sprintf("%s%s\n", $color, $text);
        echo self::RESET;
    }
}
