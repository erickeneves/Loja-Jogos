<?php
$imageMapping = [
    'god_of_war_ragnarok' => [
        'primary' => 'god_of_war_ragnarok.png',
        'alternate' => 'kratos.png'
    ],
    'the_legend_of_zelda_tears_of_the_kingdom' => [
        'primary' => 'the_legend_of_zelda_tears_of_the_kingdom.png',
        'alternate' => 'link.png'
    ],
    'horizon_forbidden_west' => [
        'primary' => 'horizon_forbidden_west.png',
        'alternate' => 'aloy.png'
    ],
    'dishonored' => [
        'primary' => 'dishonored.png',
        'alternate' => 'corvo.png'
    ],
    'devil_may_cry_3' => [
        'primary' => 'devil_may_cry_3.png',
        'alternate' => 'dante.png'
    ],
    'the_elder_scrolls_v_skyrim' => [
        'primary' => 'the_elder_scrolls_v_skyrim.png',
        'alternate' => 'dovahkiin.png'
    ],
    'fifa_23' => [
        'primary' => 'fifa_23.png',
        'alternate' => 'fifa.png'
    ],
    'super_mario_galaxy_2' => [
        'primary' => 'super_mario_galaxy_2.png',
        'alternate' => 'mario2.png'
    ],
    'grand_theft_auto_v' => [
        'primary' => 'grand_theft_auto_v.png',
        'alternate' => 'mft.png'
    ],
    'starfield' => [
        'primary' => 'starfield.png',
        'alternate' => 'starfield2.png'
    ]
];

function getGameImages($gameTitle) {
    global $imageMapping;
    
    // Normaliza o título para a chave do array
    $key = strtolower(str_replace([' ', ':', "'", ','], ['_', '', '', ''], $gameTitle));
    $key = str_replace('__', '_', $key); // Remove duplos underscores
    
    if (isset($imageMapping[$key])) {
        return [
            'primary' => $imageMapping[$key]['primary'],
            'alternate' => $imageMapping[$key]['alternate']
        ];
    }
    
    // Retorno padrão se não encontrar
    return [
        'primary' => 'default.png',
        'alternate' => 'default.png'
    ];
}
?>