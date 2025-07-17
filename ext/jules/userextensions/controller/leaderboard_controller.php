<?php
namespace jules\userextensions\controller;

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
        $sql = 'SELECT user_id, username, user_xp, user_level
                FROM ' . USERS_TABLE . '
                ORDER BY user_xp DESC
                LIMIT 10';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result)) {
            $this->template->assign_block_vars('leaderboard_row', [
                'USERNAME'  => $row['username'],
                'XP'        => $row['user_xp'],
                'LEVEL'     => $row['user_level'],
            ]);
        }
        $this->db->sql_freeresult($result);

        return $this->template->render('leaderboard_body.html');
    }
}
