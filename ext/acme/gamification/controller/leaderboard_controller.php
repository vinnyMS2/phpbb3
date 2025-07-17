<?php
namespace acme\gamification\controller;

class leaderboard_controller
{
    protected $db;
    protected $template;

    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template)
    {
        $this->db = $db;
        $this->template = $template;
    }

    public function handle()
    {
        $sql = 'SELECT u.user_id, u.username, u.user_colour, gu.user_xp, gu.user_level
            FROM ' . USERS_TABLE . ' u
            JOIN ' . $this->table_prefix . 'gamification_users gu ON u.user_id = gu.user_id
            ORDER BY gu.user_xp DESC';
        $result = $this->db->sql_query_limit($sql, 25);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->template->assign_block_vars('leaderboard', [
                'USERNAME'      => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                'XP'            => $row['user_xp'],
                'LEVEL'         => $row['user_level'],
            ]);
        }
        $this->db->sql_freeresult($result);

        return new \symfony\component\httpfoundation\Response($this->template->render('@acme_gamification/leaderboard.html'));
    }
}
