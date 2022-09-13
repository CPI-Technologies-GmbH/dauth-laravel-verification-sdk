<?php

return [
    'eth' => [
        'url' => env('ETH_URL'),
    ],
    'contract' => [
        'address' => env('MAXTRAX_CONTRACT_ADDRESS'),
    ],
    'ipfs' => [
        'server' => env('IPFS_SERVER', 'ipfs.infura.io'),
        'port' => env('IPFS_PORT', 5001),
        'apiPort' => env('IPFS_APIPORT', 5001),
        'headers' => env('IPFS_INFURA', false) ? ['Authorization: Basic ' . base64_encode(env('IPFS_INFURA_PROJECT_ID', '') . ':' . env('IPFS_INFURA_PROJECT_SECRET', ''))] : [],
    ],
    'content' => [
        'description' => 'MaxTrax is a non-transferable personal identity KYC, that other dApps can use to verify your identity. The data is stored fully decrypted on IPFS so that only the owner decides, who will see the personal information. The MaxTrax Token is mintable at https://maxtrax.me',
        'external_url' => 'https://maxtrax.me',
        'image_path' => 'https://image.maxtrax.me/',
        'version' => '1.0.0'
    ],
    'microserviceUrl' => env('CRYPTO_MICROSERVICE_URL'),
];
