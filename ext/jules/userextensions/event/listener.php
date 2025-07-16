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
    }
}
