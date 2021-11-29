<?php 

require_once "src/LTIPushData.php";
require_once "src/LTIResults/LTIPushResult.php";
require_once "src/LTIResults/LTIGetAccountListResult.php";
require_once "src/LTIResults/LTIVersionResult.php";

abstract class LTIHandler {

	public function sendResponse(string $responseXml) {
		header('Content-type: application/xml; charset=utf-8');
		header('Content-Length', strlen($responseXml)); 
		echo $responseXml;
	}

	public abstract function isTokenValid(string $token): bool;
    
	public abstract function handleActionPush(LTIPushData $data): LTIPushResult;

	public abstract function handleActionGetAccountList(): LTIGetAccountListResult;

	public abstract function handleActionGetVersion(): LTIVersionResult;
}

?>