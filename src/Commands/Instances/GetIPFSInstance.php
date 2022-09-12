<?php

namespace MaxTrax\ChallengeSDK\Commands\Instances;

use MaxTrax\ChallengeSDK\Http\Client\IPFSClient;

class GetIPFSInstance
{
    protected $instance = null;

    public function run(): IPFSClient {
        if($this->instance === null) {
            $this->instance = new IPFSClient(
                config('maxtrax.ipfs.server'),
                config('maxtrax.ipfs.port'),
                config('maxtrax.ipfs.apiPort')
            );
        }

        return $this->instance;
    }
}
