<?php

namespace App\Enum;

enum ReferentialTypeEnum: string
{
    case MONSTER_TYPE = 'monster_type';
    case ELEMENT = 'element';
    case AILMENT = 'ailment';
    case QUEST_CLIENT = 'quest_client';
    case MAP = 'map';
    case QUEST_TYPE = 'quest_type';
    case ITEM_TYPE = 'item_type';
    case WEAPON_TYPE = 'weapon_type';
}
