<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei;

abstract class LTIHandler {
    /**
     * This method can be overwritten by you if you wish to extend the response
     * with useful debugging information. See LTIVersionResult for more details.
     */
    public function handleActionGetVersion(): \ITRechtKanzlei\LTIVersionResult {
        return new \ITRechtKanzlei\LTIVersionResult();
    }

    /**
     * This method can be overwritten by you, if your system uses tokens to authenticate.
     * Please add a check mechanism to check whether the sent token is valid or not.
     * Singleshop systems might implement the check like it is made in the example.php file.
     * Multishop systems maybe need database select to check the token.
     */
    public function isTokenValid(string $token): bool {
        return false;
    }

    /**
     * This method can be overwritten by you, if your systen requires a username and password to authenticate.
     * Please add a check mechanism to check whether the sent username and passowrd is valid or not.
     */
    public function validateUserPass(string $username, string $password): bool {
         return false;
    }

    /**
     * This method must be overwritten by you. Please add the logic to push the received file to your shop system.
     */
    public abstract function handleActionPush(\ITRechtKanzlei\LTIPushData $data): \ITRechtKanzlei\LTIPushResult;

    /**
     * You can override this method if your system is a multishop system and / or
     * if you want to list the supported languages for your system / for each
     * sales channel.
     *
     * Please refer to the documentation for more details.
     */
    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        return new LTIAccountListResult();
    }

}
