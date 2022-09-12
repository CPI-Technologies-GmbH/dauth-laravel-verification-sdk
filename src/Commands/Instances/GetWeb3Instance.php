<?php

namespace MaxTrax\ChallengeSDK\Commands\Instances;

use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

class GetWeb3Instance
{
    protected $instance = null;

    public function run(): Web3 {
        if($this->instance === null) {
            $this->instance = new Web3(new HttpProvider(new HttpRequestManager(config('maxtrax.eth.url'))));
        }

        return $this->instance;
    }
}
