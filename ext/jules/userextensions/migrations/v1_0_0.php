<?php
namespace jules\userextensions\migrations;

class v1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_table_exists($this->table_prefix . 'user_badges');
    }

    static public function depends_on()
    {
        return ['\phpbb\db\migration\data\v310\beta1'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['userextensions_badges_enabled', 1]],
            ['config.add', ['userextensions_cover_images_enabled', 1]],
            ['config.add', ['userextensions_discord_enabled', 1]],
            ['config.add', ['userextensions_telegram_enabled', 1]],

            ['table.add', [
                'name' => $this->table_prefix . 'user_badges',
                'columns' => [
                    'badge_id' => ['UINT', null, 'auto_increment'],
                    'user_id' => ['UINT', 0],
                    'badge_name' => ['VCHAR:255', ''],
                    'badge_image' => ['VCHAR:255', ''],
                ],
                'primary_key' => 'badge_id',
                'keys' => [
                    'user_id' => ['INDEX', 'user_id'],
                ],
            ]],

            ['table.add_column', [
                'table_name' => $this->table_prefix . 'users',
                'column_name' => 'user_cover_img',
                'column_type' => ['VCHAR:255', ''],
            ]],
            ['table.add_column', [
                'table_name' => $this->table_prefix . 'users',
                'column_name' => 'user_discord',
                'column_type' => ['VCHAR:255', ''],
            ]],
            ['table.add_column', [
                'table_name' => $this->table_prefix . 'users',
                'column_name' => 'user_telegram',
                'column_type' => ['VCHAR:255', ''],
            ]],
        ];
    }
}
