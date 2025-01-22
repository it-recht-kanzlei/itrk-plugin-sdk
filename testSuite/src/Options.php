<?php

namespace PluginSDKTestSuite;

class Options
{
    public $url = '';
    public $token = '';
    public $userAccountId = '';
    public $postForm = false;
    public $verbose = false;

    public function __construct(
        string $url,
        string $token,
        string $userAccountId = '',
        bool $postForm = false,
        bool $verbose = false
    ) {
        $this->url = $url;
        $this->token = $token;
        $this->userAccountId = $userAccountId;
        $this->postForm = $postForm;
        $this->verbose = $verbose;
    }
}
