<?php
namespace Brave\ForumAuth;

/**
 * phpBB integration.
 *
 * Functions are copied from old forum auth (webroot/helper.php) and adjusted
 * where needed (https://github.com/bravecollective/oldcore-forum-auth)
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

    /**
     * @var array
     */
    private $cfg_bb_group_default_by_tag;

    /**
     * @var array
     */
    private $cfg_bb_group_by_tag;

    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
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

    public function __construct(array $cfg_bb_groups, array $cfg_bb_group_default_by_tag, array $cfg_bb_group_by_tag)
    {
        $this->cfg_bb_groups = $cfg_bb_groups;
        $this->cfg_bb_group_default_by_tag = $cfg_bb_group_default_by_tag;
        $this->cfg_bb_group_by_tag = $cfg_bb_group_by_tag;

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

    /*function brave_bb_account_activate($user_name)
    {
        $user_id = $this->brave_bb_user_name_to_id($user_name);
        if (! $user_id) {
            return;
        }

        user_active_flip('activate', $user_id);

        $this->brave_bb_account_update($user_name);
    }*/

    /*function brave_bb_account_deactivate($user_name)
    {
        $user_id = $this->brave_bb_user_name_to_id($user_name);
        if (! $user_id) {
            return;
        }

        user_active_flip('deactivate', $user_id);

        $this->brave_bb_account_update($user_name);
    }*/

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

    /**
     *
     * @param string $tag
     * @return array
     */
    private function brave_tag_to_group_ids($tag)
    {
        $shorts = $this->cfg_bb_group_by_tag[$tag];
        if (! $shorts) {
            return array();
        }
        if (! is_array($shorts)) {
            $shorts = array(
                $shorts
            );
        }

        $ids = array();
        foreach ($shorts as $short) {
            $id = $this->cfg_bb_groups[$short];
            if (! $id) {
                continue;
            }
            $ids[] = $id;
        }

        return $ids;
    }

    /**
     *
     * @param int $user_id forum user ID
     * @param array $main
     */
    public function brave_bb_account_update($user_id, array $character)
    {
        /* @var $cp \phpbb\profilefields\manager */
        $cp = $this->phpbb_container->get('profilefields.manager');

        $cp_data = array();
        $cp_data['pf_core_corp_name'] = $character['corporation_name'];
        $cp_data['pf_core_alli_name'] = $character['alliance_name'];
        $cp->update_profile_field_data($user_id, $cp_data);

        // DO GROUP MAGIC

        $tags = explode(",", $character['core_tags']);
        $tags = array_unique($tags);
        asort($tags);

        $gid_default = $this->cfg_bb_groups[$this->cfg_bb_group_default_by_tag['default'][1]];

        $i = 0;
        foreach ($tags as $tag) {
            $gs = $this->cfg_bb_group_default_by_tag[$tag];
            if (! $gs) {
                continue;
            }
            $gid = $this->cfg_bb_groups[$gs[1]];
            if (! $gid || $gs[0] < $i) {
                continue;
            }
            $i = $gs[0];
            $gid_default = $gid;
        }

        $gids_want = array();
        $gids_want[] = $gid_default;
        $gids_want[] = $this->cfg_bb_groups['register'];
        foreach ($tags as $t) {
            $ids = $this->brave_tag_to_group_ids($t);
            foreach ($ids as $id) {
                $gids_want[] = $id;
            }
        }
        $gids_want = array_unique($gids_want);

        $gids_has = array();
        foreach (group_memberships(false, array(
            $user_id
        ), false) as $g) {
            $gid = $g['group_id'];
            if (! in_array($gid, $gids_want)) {
                group_user_del($gid, $user_id);
                continue;
            }
            $gids_has[] = $gid;
        }

        foreach ($gids_want as $gid) {
            if (in_array($gid, $gids_has)) {
                continue;
            }
            group_user_add($gid, $user_id, false, false, false);
        }

        group_set_user_default($gid_default, array(
            $user_id
        ), false, true);
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
