<?php
namespace acme\gamification\migrations;

class v1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_table_exists($this->table_prefix . 'gamification_users');
    }

    static public function depends_on()
    {
        return ['\phpbb\db\migration\data\v33x\v334'];
    }

    public function update_data()
    {
        return [
            ['table_add', [
                'table_name'    => $this->table_prefix . 'gamification_users',
                'columns'       => [
                    'user_id'       => ['UINT', 0],
                    'user_xp'       => ['UINT', 0],
                    'user_level'    => ['UINT', 1],
                ],
                'primary_key'   => 'user_id',
            ]],
            ['table_add', [
                'table_name'    => $this->table_prefix . 'gamification_levels',
                'columns'       => [
                    'level_id'      => ['UINT', null, 'auto_increment'],
                    'level_name'    => ['VCHAR:255', ''],
                    'level_xp'      => ['UINT', 0],
                ],
                'primary_key'   => 'level_id',
            ]],
            ['table_add', [
                'table_name'    => $this->table_prefix . 'gamification_rewards',
                'columns'       => [
                    'reward_id'     => ['UINT', null, 'auto_increment'],
                    'level_id'      => ['UINT', 0],
                    'reward_name'   => ['VCHAR:255', ''],
                    'reward_type'   => ['VCHAR:255', ''],
                ],
                'primary_key'   => 'reward_id',
            ]],
        ];
    }
}
