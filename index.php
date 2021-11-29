<?php
    $postData = $_POST;
    require_once "src/LTI.php";
    require_once "src/LTIHandler.php";

    class MyLTIHandler extends LTIHandler {     
        public function isTokenValid(string $token): bool {
            return $token == '08f2c4790046cb91194925f6676e56c2';
        }


    }

    $ltiHandler = new MyLTIHandler();

    $lti = new LTI($ltiHandler);
    $result = $lti->handleRequest($postData['xml']);

    $lti->setMultiShopTrue();
    $result = $lti->handleRequest($postData['xml']);
    $var = $result->getUserAccountId();

    $list = Array(1 => 'name1', 2 => 'name2', 3 => 'name3');
    $lti->getAccoutList($list);
?>

