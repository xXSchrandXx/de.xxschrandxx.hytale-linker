<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObjectList;

/**
 * UserToUserHytale List class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class UserToHytaleUserList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserToHytaleUser::class;
}
