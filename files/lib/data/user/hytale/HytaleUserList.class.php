<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObjectList;

/**
 * HytaleUser List class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class HytaleUserList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = HytaleUser::class;
}
