<?php
namespace jules\userextensions\acp;

class userextensions_info
{
    public function module()
    {
        return [
            'filename'  => '\jules\userextensions\acp\userextensions_module',
            'title'     => 'ACP_USEREXTENSIONS_TITLE',
            'modes'     => [
                'settings' => ['title' => 'ACP_USEREXTENSIONS_SETTINGS', 'cat' => ['ACP_CAT_DOT_MODS']],
            ],
        ];
    }

    public function acp()
    {
        return [
            'title' => 'ACP_USEREXTENSIONS_TITLE',
            'auth'  => 'ext_jules/userextensions && acl_a_board',
            'cat'   => ['ACP_CAT_DOT_MODS'],
        ];
    }
}
