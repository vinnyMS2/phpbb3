<?php
namespace jules\userextensions\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    protected $db;
    protected $config;
    protected $template;
    protected $table_prefix;

    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\template\template $template, $table_prefix)
    {
        $this->db = $db;
        $this->config = $config;
        $this->template = $template;
        $this->table_prefix = $table_prefix;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup'                       => 'load_language_on_setup',
            'core.memberlist_view_profile_after'    => 'display_profile_extras',
            'core.viewtopic_body_postrow_custom_fields_after' => 'display_post_extras',
            'core.posting_modify_submission_errors' => 'award_xp_on_post',
            'core.like_add_after'                   => 'award_xp_on_like',
        ];
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'jules/userextensions',
            'lang_set' => 'common',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function display_profile_extras($event)
    {
        $user_id = $event['user_id'];
        $user_data = $event['user_data'];

        if (!empty($user_data['user_cover_img']))
        {
            $this->template->assign_vars([
                'S_USER_COVER_IMAGE'    => true,
                'USER_COVER_IMAGE'      => generate_board_url() . '/' . $user_data['user_cover_img'],
            ]);
        }

        $sql = 'SELECT * FROM ' . $this->table_prefix . 'user_badges WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $badges = $this->db->sql_fetchrowset($result);
        $this->db->sql_freeresult($result);

        if (!empty($badges))
        {
            $this->template->assign_var('S_USER_BADGES', true);
            foreach ($badges as $badge)
            {
                $this->template->assign_block_vars('user_badges', [
                    'BADGE_NAME'    => $badge['badge_name'],
                    'BADGE_IMAGE'   => generate_board_url() . '/' . $badge['badge_image'],
                ]);
            }
        }

        if ($this->config['userextensions_gamification_enabled'])
        {
            $this->template->assign_vars([
                'S_GAMIFICATION_ENABLED'    => true,
                'USER_LEVEL'                => $user_data['user_level'],
                'USER_XP'                   => $user_data['user_xp'],
            ]);
        }
    }

    public function display_post_extras($event)
    {
        $postrow = $event['postrow'];

        if (!empty($postrow['user_discord']))
        {
            $this->template->assign_block_vars('postrow', [
                'U_DISCORD' => 'https://discordapp.com/users/' . $postrow['user_discord'],
            ]);
        }

        if (!empty($postrow['user_telegram']))
        {
            $this->template->assign_block_vars('postrow', [
                'U_TELEGRAM' => 'https://t.me/' . $postrow['user_telegram'],
            ]);
        }

        if ($this->config['userextensions_gamification_enabled'])
        {
            $this->template->assign_block_vars('postrow', [
                'S_GAMIFICATION_ENABLED'    => true,
                'USER_LEVEL'                => $postrow['user_level'],
                'USER_XP'                   => $postrow['user_xp'],
            ]);
        }
    }

    public function award_xp_on_post($event)
    {
        if (!$this->config['userextensions_gamification_enabled'])
        {
            return;
        }

        $post_data = $event['post_data'];
        $user_id = $post_data['poster_id'];

        if ($post_data['post_id'])
        {
            // This is a reply
            $xp_to_award = $this->config['userextensions_xp_per_reply'];
        }
        else
        {
            // This is a new topic
            $xp_to_award = $this->config['userextensions_xp_per_post'];
        }

        $this->add_xp($user_id, $xp_to_award);
    }

    public function award_xp_on_like($event)
    {
        if (!$this->config['userextensions_gamification_enabled'])
        {
            return;
        }

        $user_id = $event['user_id'];
        $xp_to_award = $this->config['userextensions_xp_per_like'];

        $this->add_xp($user_id, $xp_to_award);
    }

    private function add_xp($user_id, $xp)
    {
        $sql = 'UPDATE ' . $this->table_prefix . 'users
                SET user_xp = user_xp + ' . (int) $xp . '
                WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);

        $this->check_for_level_up($user_id);
    }

    private function check_for_level_up($user_id)
    {
        $sql = 'SELECT user_xp, user_level
                FROM ' . $this->table_prefix . 'users
                WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $user_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        $sql = 'SELECT level_id, level_xp
                FROM ' . $this->table_prefix . 'user_levels
                WHERE level_xp > ' . (int) $user_data['user_xp'] . '
                ORDER BY level_xp ASC
                LIMIT 1';
        $result = $this->db->sql_query($sql);
        $next_level = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($next_level && $user_data['user_level'] < $next_level['level_id'])
        {
            $sql = 'UPDATE ' . $this->table_prefix . 'users
                    SET user_level = ' . (int) $next_level['level_id'] . '
                    WHERE user_id = ' . (int) $user_id;
            $this->db->sql_query($sql);
        }
    }
}
