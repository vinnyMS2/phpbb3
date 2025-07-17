<?php
namespace jules\userextensions\controller;

class shop_controller
{
    protected $db;
    protected $template;
    protected $user;

    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user)
    {
        $this->db = $db;
        $this->template = $template;
        $this->user = $user;
    }

    public function handle()
    {
        $sql = 'SELECT item_id, item_name, item_description, item_cost
                FROM ' . $this->table_prefix . 'user_shop_items';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result)) {
            $this->template->assign_block_vars('shop_item', [
                'ID'            => $row['item_id'],
                'NAME'          => $row['item_name'],
                'DESCRIPTION'   => $row['item_description'],
                'COST'          => $row['item_cost'],
            ]);
        }
        $this->db->sql_freeresult($result);

        return $this->template->render('shop_body.html');
    }

    public function purchase($item_id)
    {
        $sql = 'SELECT item_cost
                FROM ' . $this->table_prefix . 'user_shop_items
                WHERE item_id = ' . (int) $item_id;
        $result = $this->db->sql_query($sql);
        $item = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$item)
        {
            trigger_error('ITEM_NOT_FOUND');
        }

        if ($this->user->data['user_xp'] < $item['item_cost'])
        {
            trigger_error('NOT_ENOUGH_XP');
        }

        $sql = 'UPDATE ' . $this->table_prefix . 'users
                SET user_xp = user_xp - ' . (int) $item['item_cost'] . '
                WHERE user_id = ' . (int) $this->user->data['user_id'];
        $this->db->sql_query($sql);

        $sql_ary = [
            'user_id'   => (int) $this->user->data['user_id'],
            'item_id'   => (int) $item_id,
        ];
        $sql = 'INSERT INTO ' . $this->table_prefix . 'user_inventory ' . $this->db->sql_build_array('INSERT', $sql_ary);
        $this->db->sql_query($sql);

        trigger_error('ITEM_PURCHASED');
    }
}
