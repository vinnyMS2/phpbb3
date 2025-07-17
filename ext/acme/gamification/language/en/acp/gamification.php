<?php
if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'ACP_GAMIFICATION'                  => 'Gamification',
    'ACP_GAMIFICATION_SETTINGS'         => 'Gamification Settings',
    'ACP_GAMIFICATION_SETTINGS_CHANGED' => 'Gamification settings successfully changed.',
    'GAMIFICATION_XP_TOPIC'             => 'XP for new topic',
    'GAMIFICATION_XP_TOPIC_EXPLAIN'     => 'The amount of XP a user receives for creating a new topic.',
    'GAMIFICATION_XP_REPLY'             => 'XP for reply',
    'GAMIFICATION_XP_REPLY_EXPLAIN'     => 'The amount of XP a user receives for replying to a topic.',
    'GAMIFICATION_XP_LIKE'              => 'XP for like',
    'GAMIFICATION_XP_LIKE_EXPLAIN'      => 'The amount of XP a user receives for liking a post.',
    'ACP_GAMIFICATION_LEVELS'           => 'Levels',
    'ACP_GAMIFICATION_LEVELS_EXPLAIN'   => 'Here you can manage the levels for the gamification system.',
    'ACP_GAMIFICATION_LEVEL_ADDED'      => 'Level successfully added.',
    'LEVEL_NAME'                        => 'Level Name',
    'LEVEL_XP'                          => 'XP Required',
    'ADD_LEVEL'                         => 'Add Level',
]);
