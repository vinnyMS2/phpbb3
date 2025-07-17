<?php
namespace jules\userextensions\migrations;

class v1_0_1 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_table_exists($this->table_prefix . 'user_levels');
    }

    static public function depends_on()
    {
        return ['\jules\userextensions\migrations\v1_0_0'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['userextensions_gamification_enabled', 1]],
            ['config.add', ['userextensions_xp_per_post', 10]],
            ['config.add', ['userextensions_xp_per_reply', 5]],
            ['config.add', ['userextensions_xp_per_like', 1]],

            ['table.add', [
                'name' => $this->table_prefix . 'user_levels',
                'columns' => [
                    'level_id' => ['UINT', null, 'auto_increment'],
                    'level_name' => ['VCHAR:255', ''],
                    'level_xp' => ['UINT', 0],
                ],
                'primary_key' => 'level_id',
            ]],

            ['table.add_column', [
                'table_name' => $this->table_prefix . 'users',
                'column_name' => 'user_xp',
                'column_type' => ['UINT', 0],
            ]],
            ['table.add_column', [
                'table_name' => $this->table_prefix . 'users',
                'column_name' => 'user_level',
                'column_type' => ['UINT', 0],
            ]],
        ];
    }
}
