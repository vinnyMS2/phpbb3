<?php
namespace jules\userextensions\acp;

class userextensions_module
{
    public $u_action;

    public function main($id, $mode)
    {
        global $config, $request, $template, $user;

        $this->tpl_name = 'acp_userextensions';
        $this->page_title = 'ACP_USEREXTENSIONS_TITLE';

        $form_key = 'acp_userextensions';
        add_form_key($form_key);

        $submit = $request->is_set_post('submit');

        if ($submit) {
            if (!check_form_key($form_key)) {
                trigger_error('FORM_INVALID', E_USER_WARNING);
            }

            $config->set('userextensions_badges_enabled', $request->variable('userextensions_badges_enabled', 0));
            $config->set('userextensions_cover_images_enabled', $request->variable('userextensions_cover_images_enabled', 0));
            $config->set('userextensions_discord_enabled', $request->variable('userextensions_discord_enabled', 0));
            $config->set('userextensions_telegram_enabled', $request->variable('userextensions_telegram_enabled', 0));

            trigger_error('ACP_USEREXTENSIONS_SETTINGS_CHANGED' . adm_back_link($this->u_action));
        }

        $template->assign_vars([
            'U_ACTION'                          => $this->u_action,
            'USEREXTENSIONS_BADGES_ENABLED'     => $config['userextensions_badges_enabled'],
            'USEREXTENSIONS_COVER_IMAGES_ENABLED' => $config['userextensions_cover_images_enabled'],
            'USEREXTENSIONS_DISCORD_ENABLED'    => $config['userextensions_discord_enabled'],
            'USEREXTENSIONS_TELEGRAM_ENABLED'   => $config['userextensions_telegram_enabled'],
            'USEREXTENSIONS_GAMIFICATION_ENABLED' => $config['userextensions_gamification_enabled'],
            'USEREXTENSIONS_XP_PER_POST'        => $config['userextensions_xp_per_post'],
            'USEREXTENSIONS_XP_PER_REPLY'       => $config['userextensions_xp_per_reply'],
            'USEREXTENSIONS_XP_PER_LIKE'        => $config['userextensions_xp_per_like'],
        ]);
    }
}
