<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObjectEditor;

/**
 * UserToUserHytale Editor class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class UserToHytaleUserEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserToHytaleUser::class;
}
