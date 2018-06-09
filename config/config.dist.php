<?php

return [
    // SSO CONFIGURATION
    'SSO_CLIENT_ID' => '',
    'SSO_CLIENT_SECRET' => '',
    'SSO_REDIRECTURI' => 'http://localhost:8080/auth',
    'SSO_URL_AUTHORIZE' => 'https://login.eveonline.com/oauth/authorize',
    'SSO_URL_ACCESSTOKEN' => 'https://login.eveonline.com/oauth/token',
    'SSO_URL_RESOURCEOWNERDETAILS' => 'https://esi.tech.ccp.is/verify/',
    'SSO_SCOPES' => '',

    // App
    'brave.serviceName' => 'Forum Authentication',

    // NEUCORE
    'CORE_URL' => 'https://account.bravecollective.com/api',
    'CORE_APP_ID' => '',
    'CORE_APP_TOKEN' => '',

    // DB
    'DB_URL' => 'mysql://root:vagrant@localhost/forum_auth',

    // Slim
    'displayErrorDetails' => true,
];
