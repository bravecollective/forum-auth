<?php
namespace Brave\ForumAuth;

/**
 * phpBB integration.
 *
 * Functions copied from old forum auth and adjusted where needed.
 * https://github.com/bravecollective/oldcore-forum-auth
 *
 * This needs two custom profile fields (Single text fields):
 * core_corp_name
 * core_alli_name
 *
 * See config/config.php for groups.
 */
class PhpBB
{
    /**
     * @var array
     */
    private $cfg_bb_groups;

    private $phpbb_container;

    /**
     * @var \phpbb\config\db
     */
    private $config;

    /**
     * @var \phpbb\db\driver\driver_interface
     */
    private $db;

    /**
     * @var \phpbb\user
     */
    private $user;

    public function __construct(array $cfg_bb_groups)
    {
        $this->cfg_bb_groups = $cfg_bb_groups;

        global $phpbb_container, $config, $db, $user;

        $this->phpbb_container = $phpbb_container;
        $this->config = $config;
        $this->db = $db;
        $this->user = $user;
    }

    public function brave_bb_user_name_to_id($user_name)
    {
        $user_names = array(
            $user_name
        );
        $user_ids = array();
        $result = user_get_id_name($user_ids, $user_names);
        if ($result) {
            return false;
        }
        if (sizeof($user_ids) == 1) {
            return $user_ids[0];
        }

        return false;
    }

    public function brave_bb_account_create($character_id, $user_name, $password, $ipAddress)
    {
        $passwords_manager = $this->phpbb_container->get('passwords.manager');

        $user = array(
            'username' => $user_name,
            'user_password' => $passwords_manager->hash($password),
            'user_email' => '',
            'group_id' => $this->cfg_bb_groups['register'],
            'user_type' => USER_NORMAL,
            'user_ip' => $ipAddress,
            'user_new' => ($this->config['new_member_post_limit']) ? 1 : 0,
            'user_avatar' => 'https://image.eveonline.com/Character/' . $character_id . '_128.jpg',
            'user_avatar_type' => 2,
            'user_avatar_width' => 128,
            'user_avatar_height' => 128
        );

        user_add($user);

        $user_id = $this->brave_bb_user_name_to_id($user_name);
        add_log('user', $user_id, 'LOG_USER_GENERAL', 'Created user through CORE');

        return $user_id;
    }

    public function brave_bb_account_password($user_id, $password)
    {
        $passwords_manager = $this->phpbb_container->get('passwords.manager');

        $sql_ary = array(
            'user_password' => $passwords_manager->hash($password),
            'user_passchg' => time()
        );

        $sql = 'UPDATE ' . USERS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) .
            ' WHERE user_id = ' . $user_id;
        $this->db->sql_query($sql);

        $this->user->reset_login_keys($user_id);

        add_log('user', $user_id, 'LOG_USER_NEW_PASSWORD', 'Reset password throuh CORE');
    }
}
