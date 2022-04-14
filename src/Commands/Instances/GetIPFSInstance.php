<?php

namespace DAuth\ChallengeSDK\Commands\Instances;

use DAuth\ChallengeSDK\Http\Client\IPFSClient;

class GetIPFSInstance
{
    protected $instance = null;

    public function run(): IPFSClient {
        if($this->instance === null) {
            $this->instance = new IPFSClient(
                config('dauth.ipfs.server'),
                config('dauth.ipfs.port'),
                config('dauth.ipfs.apiPort')
            );
        }

        return $this->instance;
    }
}
