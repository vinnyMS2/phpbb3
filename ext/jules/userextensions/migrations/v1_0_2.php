<?php
namespace jules\userextensions\migrations;

class v1_0_2 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return $this->db_tools->sql_table_exists($this->table_prefix . 'user_shop_items');
    }

    static public function depends_on()
    {
        return ['\jules\userextensions\migrations\v1_0_1'];
    }

    public function update_data()
    {
        return [
            ['table.add', [
                'name' => $this->table_prefix . 'user_shop_items',
                'columns' => [
                    'item_id' => ['UINT', null, 'auto_increment'],
                    'item_name' => ['VCHAR:255', ''],
                    'item_description' => ['TEXT', ''],
                    'item_cost' => ['UINT', 0],
                ],
                'primary_key' => 'item_id',
            ]],

            ['table.add', [
                'name' => $this->table_prefix . 'user_inventory',
                'columns' => [
                    'inventory_id' => ['UINT', null, 'auto_increment'],
                    'user_id' => ['UINT', 0],
                    'item_id' => ['UINT', 0],
                ],
                'primary_key' => 'inventory_id',
                'keys' => [
                    'user_id' => ['INDEX', 'user_id'],
                    'item_id' => ['INDEX', 'item_id'],
                ],
            ]],
        ];
    }
}
