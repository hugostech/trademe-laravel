<?php

return [
    'mode'=>env('TM_MODE', 'sandbox'), //sandbox, production
    'tm_consumer_key'=>env('TM_CONSUMER_KEY'),
    'tm_consumer_secret'=>env('TM_CONSUMER_SECRET'),
    'oauth_token'=>env('TM_OAUTH_TOKEN'),
    'oauth_token_secret'=>env('TM_OAUTH_TOKEN_SECRET'),
    'tm_sandbox_url'=>'https://api.tmsandbox.co.nz/v1/',
    'tm_production_url'=>'https://api.trademe.co.nz/v1/',
];
