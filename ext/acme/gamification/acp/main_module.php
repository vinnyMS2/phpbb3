<?php
namespace acme\gamification\acp;

class main_module
{
    public $u_action;

    public function main($id, $mode)
    {
        global $config, $db, $user, $auth, $template, $cache;
        global $phpbb_root_path, $phpbb_admin_path, $phpEx;

        $this->tpl_name = 'acp_gamification';
        $this->page_title = 'ACP_GAMIFICATION';

        $form_key = 'acme_gamification_acp';
        add_form_key($form_key);

        if (isset($_POST['submit']))
        {
            if (!check_form_key($form_key))
            {
                trigger_error('FORM_INVALID');
            }

            $config->set('gamification_xp_topic', request_var('gamification_xp_topic', 0));
            $config->set('gamification_xp_reply', request_var('gamification_xp_reply', 0));
            $config->set('gamification_xp_like', request_var('gamification_xp_like', 0));

            trigger_error('ACP_GAMIFICATION_SETTINGS_CHANGED' . adm_back_link($this->u_action));
        }

        $template->assign_vars([
            'U_ACTION'              => $this->u_action,
            'GAMIFICATION_XP_TOPIC' => $config['gamification_xp_topic'],
            'GAMIFICATION_XP_REPLY' => $config['gamification_xp_reply'],
            'GAMIFICATION_XP_LIKE'  => $config['gamification_xp_like'],
        ]);

        // Levels and Rewards
        if ($mode == 'levels')
        {
            $this->tpl_name = 'acp_gamification_levels';
            $this->page_title = 'ACP_GAMIFICATION_LEVELS';

            if (isset($_POST['add_level']))
            {
                $level_name = request_var('level_name', '');
                $level_xp = request_var('level_xp', 0);

                $sql_ary = [
                    'level_name'    => $level_name,
                    'level_xp'      => $level_xp,
                ];
                $sql = 'INSERT INTO ' . $this->table_prefix . 'gamification_levels ' . $db->sql_build_array('INSERT', $sql_ary);
                $db->sql_query($sql);

                trigger_error('ACP_GAMIFICATION_LEVEL_ADDED' . adm_back_link($this->u_action));
            }

            $sql = 'SELECT * FROM ' . $this->table_prefix . 'gamification_levels ORDER BY level_xp ASC';
            $result = $db->sql_query($sql);

            while ($row = $db->sql_fetchrow($result))
            {
                $template->assign_block_vars('levels', [
                    'LEVEL_ID'      => $row['level_id'],
                    'LEVEL_NAME'    => $row['level_name'],
                    'LEVEL_XP'      => $row['level_xp'],
                ]);
            }
            $db->sql_freeresult($result);
        }
    }
}
