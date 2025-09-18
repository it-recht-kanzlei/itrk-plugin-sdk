<?php
/*
 * Please do NOT edit this class to ensure that the code remains executable.
 */

namespace ITRechtKanzlei\LegalText\Sdk;

abstract class LTIHandler {
    /**
     * This method can be used to initialize resources or to validate preconditions
     * that the target system has to fulfill to operate.
     * If the necessary conditions are not met, you can throw an exception here
     * which will be converted to a properly formatted error response.
     * @throws \Exception
     */
    public function preHandleRequest(): void {}

    /**
     * You can overwrite this method if your system uses tokens to authenticate.
     * Please add a check mechanism to check whether the received token is valid.
     */
    public function isTokenValid(string $token): bool {
        return false;
    }

    /**
     * You can overwrite this method if your system requires a username and password to authenticate.
     * Please add a check mechanism to check whether the received username and password are valid..
     */
    public function validateUserPass(string $username, string $password): bool {
         return false;
    }

    /**
     * You can overwrite this method if you wish to extend the response
     * with useful debugging information. See LTIVersionResult for more details.
     */
    public function handleActionGetVersion(): LTIVersionResult {
        return new LTIVersionResult();
    }

    /**
     * You must overwrite this method. Please add the logic to push the received
     * file to your shop system.
     */
    public abstract function handleActionPush(LTIPushData $data): LTIPushResult;

    /**
     * You can override this method if your system is a multishop system and / or
     * if you want to list the supported languages for your system / for each
     * sales channel.
     *
     * Please refer to the documentation for more details.
     */
    public function handleActionGetAccountList(): LTIAccountListResult {
        return new LTIAccountListResult();
    }

}
