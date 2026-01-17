<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObject;

/**
 * UserToUserHytale Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 *
 * @property-read int $userID
 * @property-read int $hytaleUserID
 */
class UserToHytaleUser extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_to_user_hytale';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'hytaleUserID';

    /**
     * Returns userID
     * @return ?int
     */
    public function getUserID()
    {
        return $this->userID;
    }
}
