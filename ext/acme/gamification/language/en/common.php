<?php
if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'GAMIFICATION'          => 'Gamification',
    'GAMIFICATION_LEVEL'    => 'Level',
    'GAMIFICATION_XP'       => 'XP',
    'LEADERBOARD'           => 'Leaderboard',
    'RANK'                  => 'Rank',
    'USERNAME'              => 'Username',
    'LEVEL'                 => 'Level',
    'XP'                    => 'XP',
]);
