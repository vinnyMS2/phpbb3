<?php
namespace acme\gamification\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
    protected $db;
    protected $config;
    protected $user;

    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\user $user)
    {
        $this->db = $db;
        $this->config = $config;
        $this->user = $user;
    }

    static public function getSubscribedEvents()
    {
        return [
            'core.posting_modify_submission_errors' => 'post_xp',
            'core.ucp_add_foes_after' => 'like_xp',
            'core.viewtopic_modify_post_row'        => 'display_xp_level',
            'core.memberlist_view_profile'          => 'display_xp_level_profile',
        ];
    }

    public function post_xp($event)
    {
        if ($this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }

        $post_data = $event['post_data'];
        if ($post_data['post_id'])
        {
            // This is a reply, not a new topic
            $this->add_xp($this->user->data['user_id'], 'reply');
        }
        else
        {
            // This is a new topic
            $this->add_xp($this->user->data['user_id'], 'topic');
        }
    }

    public function like_xp($event)
    {
        if ($this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }

        $user_id = $event['user_id'];
        $this->add_xp($user_id, 'like');
    }

    protected function add_xp($user_id, $action)
    {
        $xp_to_add = (int) $this->config['gamification_xp_' . $action];
        if ($xp_to_add <= 0)
        {
            return;
        }

        $sql = 'UPDATE ' . $this->table_prefix . 'gamification_users
            SET user_xp = user_xp + ' . $xp_to_add . '
            WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);

        if (!$this->db->sql_affectedrows())
        {
            $sql_ary = [
                'user_id'   => (int) $user_id,
                'user_xp'   => $xp_to_add,
            ];
            $sql = 'INSERT INTO ' . $this->table_prefix . 'gamification_users ' . $this->db->sql_build_array('INSERT', $sql_ary);
            $this->db->sql_query($sql);
        }

        $this->check_for_level_up($user_id);
    }

    protected function check_for_level_up($user_id)
    {
        $sql = 'SELECT user_xp, user_level
            FROM ' . $this->table_prefix . 'gamification_users
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $user_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$user_data)
        {
            return;
        }

        $sql = 'SELECT level_xp
            FROM ' . $this->table_prefix . 'gamification_levels
            WHERE level_id = ' . ($user_data['user_level'] + 1);
        $result = $this->db->sql_query($sql);
        $next_level_xp = $this->db->sql_fetchfield('level_xp');
        $this->db->sql_freeresult($result);

        if ($next_level_xp && $user_data['user_xp'] >= $next_level_xp)
        {
            $sql = 'UPDATE ' . $this->table_prefix . 'gamification_users
                SET user_level = user_level + 1
                WHERE user_id = ' . (int) $user_id;
            $this->db->sql_query($sql);

            $this->award_rewards($user_id, $user_data['user_level'] + 1);
        }
    }

    protected function award_rewards($user_id, $level_id)
    {
        $sql = 'SELECT reward_type, reward_name
            FROM ' . $this->table_prefix . 'gamification_rewards
            WHERE level_id = ' . (int) $level_id;
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            if ($row['reward_type'] == 'rank')
            {
                $sql = 'UPDATE ' . USERS_TABLE . '
                    SET user_rank = ' . (int) $row['reward_name'] . '
                    WHERE user_id = ' . (int) $user_id;
                $this->db->sql_query($sql);
            }
        }
        $this->db->sql_freeresult($result);
    }

    public function display_xp_level($event)
    {
        $post_row = $event['post_row'];
        $user_id = $post_row['user_id'];

        $sql = 'SELECT user_xp, user_level
            FROM ' . $this->table_prefix . 'gamification_users
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $user_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($user_data)
        {
            $post_row['custom_fields']['gamification_xp'] = $user_data['user_xp'];
            $post_row['custom_fields']['gamification_level'] = $user_data['user_level'];
        }

        $event['post_row'] = $post_row;
    }

    public function display_xp_level_profile($event)
    {
        $member = $event['member'];
        $user_id = $member['user_id'];

        $sql = 'SELECT user_xp, user_level
            FROM ' . $this->table_prefix . 'gamification_users
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $user_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($user_data)
        {
            $member['custom_fields']['gamification_xp'] = $user_data['user_xp'];
            $member['custom_fields']['gamification_level'] = $user_data['user_level'];
        }

        $event['member'] = $member;
    }
}
