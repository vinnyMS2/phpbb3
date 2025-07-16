<?php
if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'USER_BADGES'                       => 'User Badges',
    'USER_BADGES_EXPLAIN'               => 'Here you can manage the user badges.',
    'ADD_BADGE'                         => 'Add Badge',
    'BADGE_NAME'                        => 'Badge Name',
    'BADGE_IMAGE'                       => 'Badge Image',
    'PROFILE_COVER'                     => 'Profile Cover',
    'DISCORD'                           => 'Discord',
    'TELEGRAM'                          => 'Telegram',
    'ACP_USEREXTENSIONS_TITLE'          => 'User Extensions',
    'ACP_USEREXTENSIONS_SETTINGS'       => 'Settings',
    'ACP_USEREXTENSIONS_SETTINGS_CHANGED' => 'Settings changed successfully.',
    'USEREXTENSIONS_BADGES_ENABLED'     => 'Enable badges',
    'USEREXTENSIONS_COVER_IMAGES_ENABLED' => 'Enable cover images',
    'USEREXTENSIONS_DISCORD_ENABLED'    => 'Enable Discord link',
    'USEREXTENSIONS_TELEGRAM_ENABLED'   => 'Enable Telegram link',
]);
