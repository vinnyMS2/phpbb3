<?php
namespace jules\userextensions;

class ext extends \phpbb\extension\base
{
    public function is_enableable()
    {
        return true;
    }

    public function enable_step($old_state)
    {
        return parent::enable_step($old_state);
    }

    public function disable_step($old_state)
    {
        return parent::disable_step($old_state);
    }

    public function purge_step($old_state)
    {
        return parent::purge_step($old_state);
    }
}
