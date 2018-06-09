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
    'cfg_bb_group_by_tag' => array(
        'admin'                           => array('admin'),
        'alliance.cnm.brass'              => array('concord', 'cnm', 'recon_interaction'),
        'alliance.cnm.ceo'                => array('cnm'),
        'alliance.cnm.ceo.secondary'      => array('cnm'),
        'alliance.cnm.division'           => array('cnm'),
        'alliance.cnm.division.secondary' => array('cnm'),

        'alliance.edu'                 => array('dojo_mod', 'dojo_staff'),
        'alliance.edu.department_head' => array('dojo_mod', 'dojo_staff'),
        'alliance.edu.officer'         => array('dojo_mod', 'dojo_staff'),
        'alliance.edu.secondary'       => array('dojo_mod', 'dojo_staff'),

        'alliance.edu.sempai' => array('dojo_staff'),
        'alliance.edu.sensei' => array('dojo_staff'),

        'forums.group.concord' => array('concord'),

        'alliance.sig.braveheart'         => array('braveheart_mod'),
        'alliance.sig.braveheart.officer' => array('braveheart_mod'),

        #'alliance.sig.hipposim'         => array('hipposim_mod', 'hipposim_directorate_user', 'hipposim_directorate_mod'),
        #'alliance.sig.hipposim.officer' => array('hipposim_mod', 'hipposim_directorate_user'),

        'alliance.corporation.be-fr'          => array('befr', 'befr_mod'),
        'alliance.corporation.be-fr.director' => array('befr', 'befr_mod'),
        'alliance.corporation.be-fr.board'    => array('befr', 'befr_board'),
        'alliance.corporation.be-fr.member'   => array('befr'),

        #'alliance.corporation.thn'          => array('thn', 'thn_mod'),
        #'alliance.corporation.thn.director' => array('thn', 'thn_mod'),
        #'alliance.corporation.thn.member'   => array('thn'),

        'alliance.hr'         => array('hr', 'hr_mod'),
        'alliance.hr.officer' => array('hr', 'hr_mod'),
        'alliance.hr.minion'  => array('hr'),

        'alliance.industry'         => array('industry_mod'),
        'alliance.industry.officer' => array('industry_mod'),

        'alliance.recon'           => array('recon_user', 'recon_interaction', 'recon_officer', 'recon_director'),
        'alliance.recon.secondary' => array('recon_user', 'recon_interaction', 'recon_officer', 'recon_director'),
        'alliance.recon.director'  => array('recon_user', 'recon_interaction', 'recon_officer', 'recon_director'),
        'alliance.recon.officer'   => array('recon_user', 'recon_interaction', 'recon_officer'),
        'alliance.recon.member'    => array('recon_user', 'recon_interaction'),
        'alliance.recon.trial'     => array('recon_user', 'recon_interaction'),

        'alliance.mil'                => array('recon_interaction', 'military_mod', 'military_cap_mod', 'military_cap_member'),
        'alliance.mil.capital'        => array('military_cap_mod', 'military_cap_member'),
        'alliance.mil.capital.fc'     => array('military_cap_mod', 'military_cap_member'),
        'alliance.mil.capital.member' => array('military_cap_member'),

        'alliance.mil.fc.mildir' => array('recon_interaction', 'military_mod'),
        'alliance.mil.fc.full'   => array('recon_interaction'),
        'alliance.mil.fc.junior' => array('recon_interaction'),

        'alliance.tournament'             => array('tournament_user', 'tournament_mod'),
        #'alliance.sig.tournament.officer' => array('tournament_user', 'tournament_mod'),
        'alliance.tournament.member'      => array('tournament_user'),

        'alliance.sig.jitastanding'         => array('jitastanding_user', 'jitastanding_mod'),
        'alliance.sig.jitastanding.officer' => array('jitastanding_user', 'jitastanding_mod'),
        'alliance.sig.jitastanding.member'  => array('jitastanding_user'),
        'alliance.sig.parroto'              => array('parroto_mod', 'parroto_user'),
        'alliance.sig.parroto.member'       => array('parroto_user'),

        'alliance.sig.blackops'          => array('blackops_mod', 'blackops_user', 'blackops_res'),
        'alliance.sig.blackops.member'   => array('blackops_user'),
        'alliance.sig.blackops.reserves' => array('blackops_res'),

        'alliance.corporation.bni.member'           => array('bni_member'),
        'alliance.corporation.bni.mods'             => array('bni_mod'),
        'alliance.corporation.bni.leadership'       => array('bni_mod', 'bni_leadership'),
        'alliance.corporation.bni.recruiters.board' => array('bni_recruiter'),

        'alliance.corporation.z--in'          => array('zin_member', 'zin_mod'),
        'alliance.corporation.z--in.director' => array('zin_member', 'zin_mod'),
        'alliance.corporation.z--in.member'   => array('zin_member'),

        'alliance.corporation.incredible'            => array('incredible_leadership'),
        'alliance.corporation.incredible.leadership' => array('incredible_leadership'),
        'alliance.corporation.incredible.member'     => array('incredible_member'),

        'alliance.sig.newsnetwork' => array('bnn_member')
    ),
    'cfg_bb_group_default_by_tag' => array(
        'default' => array(0, 'spai'),
        'blue'    => array(1, 'blue'),
        'member'  => array(2, 'brave'),
    )
];
