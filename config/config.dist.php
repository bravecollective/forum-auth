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

    // config copied from old forum auth
    'cfg_bb_path' => '/var/www/phpbb',
    'cfg_bb_groups' => array(
        'register' => 2,
        'inactive' => 14,
        'admin'    => 5,

        'spai'  => 10,
        'blue'  => 9,
        'brave' => 8,

        'befr'       => 20,
        'befr_mod'   => 21,
        'befr_board' => 26,

        'concord'        => 11,
        'cnm'            => 12,
        'dojo_mod'       => 13,
        'braveheart_mod' => 15,
        'dojo_staff'     => 16,

        'hr'     => 22,
        'hr_mod' => 23,

        'thn'     => 24,
        'thn_mod' => 25,

        'recon_user'        => 27,
        'recon_officer'     => 28,
        'recon_director'    => 29,
        'recon_interaction' => 30,

        'tournament_user' => 31,
        'tournament_mod'  => 32,

        'jitastanding_user' => 33,
        'jitastanding_mod'  => 34,

        'military_mod'        => 35,
        'military_cap_member' => 42,
        'military_cap_mod'    => 43,

        'industry_mod' => 36,

        'parroto_user' => 45,
        'parroto_mod'  => 46,

        'blackops_user' => 47,
        'blackops_mod'  => 48,
        'blackops_res'  => 52,

        'bni_mod'        => 37,
        'bni_leadership' => 38,
        'bni_member'     => 39,
        'bni_recruiter'  => 44,

        'zin_member' => 40,
        'zin_mod'    => 41,

        'incredible_leadership' => 49,
        'incredible_member'     => 50,

        'bnn_member' => 51
    ),
];
