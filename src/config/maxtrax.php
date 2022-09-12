<?php

return [
    'eth' => [
        'url' => env('ETH_URL'),
    ],
    'contract' => [
        'address' => env('MAXTRAX_CONTRACT_ADDRESS'),
        'abi' => resource_path('abis/maxtrax-abi.json'),
    ],
    'ipfs' => [
        'server' => env('IPFS_SERVER', 'ipfs.infura.io'),
        'port' => env('IPFS_PORT', 5001),
        'apiPort' => env('IPFS_APIPORT', 5001),
    ],
    'content' => [
        'description' => 'MaxTrax is a non-transferable personal identity KYC, that other dApps can use to verify your identity. The data is stored fully decrypted on IPFS so that only the owner decides, who will see the personal information. The MaxTrax Token is mintable at https://maxtrax.app',
        'external_url' => 'https://maxtrax.app',
        'image_path' => 'https://image.maxtrax.app/',
        'version' => '1.0.0'
    ],
    'microserviceUrl' => env('CRYPTO_MICROSERVICE_URL'),
];
