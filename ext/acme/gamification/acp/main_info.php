<?php
namespace acme\gamification\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\acme\gamification\acp\main_module',
            'title'     => 'ACP_GAMIFICATION',
            'modes'     => [
                'settings'  => ['title' => 'ACP_GAMIFICATION_SETTINGS', 'cat' => ['ACP_CAT_DOT_MODS']],
                'levels'    => ['title' => 'ACP_GAMIFICATION_LEVELS', 'cat' => ['ACP_CAT_DOT_MODS']],
            ],
        ];
    }
}
