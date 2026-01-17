<?php

namespace wcf\util;

use Exception;
use wcf\data\user\hytale\HytaleUser;
use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\data\user\User;

/**
 * HytaleLinker util class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Util
 */
class HytaleLinkerUtil extends HytaleUtil
{
    /**
     * Gets the HytaleUser from given UUID.
     * @param string $uuid UUID
     * @return ?HytaleUser
     */
    public static function getHytaleUser(string $uuid): ?HytaleUser
    {
        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUUID = ?', [$uuid]);
        if ($hytaleUserList->countObjects() !== 1) {
            return null;
        }
        $hytaleUserList->readObjects();
        return $hytaleUserList->getSingleObject();
    }

    /**
     * Gets the User from given UUID.
     * @param string $uuid UUID
     * @return ?User
     */
    public static function getUser(string $uuid): ?User
    {
        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUUID = ?', [$uuid]);
        if ($hytaleUserList->countObjects() !== 1) {
            return null;
        }
        $hytaleUserList->readObjectIDs();
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$hytaleUserList->getObjectIDs()]);
        if ($userToHytaleUserList->countObjects() !== 1) {
            return null;
        }
        $userToHytaleUserList->readObjects();
        /** @var \wcf\data\user\hytale\UserToHytaleUser */
        $userToHytaleUser = $userToHytaleUserList->getSingleObject();
        return new User($userToHytaleUser->getUserID());
    }

    /**
     * Returns unread HytaleUserList with HytaleUsers from given userID.
     * @param int $userID
     * @return HytaleUserList
     */
    public static function getHytaleUsers(int $userID): HytaleUserList
    {
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $userToHytaleUserList->readObjects();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        $hytaleUserList = new HytaleUserList();
        if (!empty($userToHytaleUserIDs)) {
            $hytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        }
        return $hytaleUserList;
    }

    /**
     * Returns unread HytaleUserList with unlinked hytale users
     * @return HytaleUserList
     */
    public static function getUnlinkedHytaleUser(): HytaleUserList
    {
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->readObjects();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        $hytaleUserList = new HytaleUserList();
        if (!empty($userToHytaleUserIDs)) {
            $hytaleUserList->getConditionBuilder()->add('hytaleUserID NOT IN (?)', [$userToHytaleUserIDs]);
        }
        return $hytaleUserList;
    }

    /**
     * Returns unread HytaleUserList with linked hytale users
     * @return HytaleUserList
     * @throws Exception when no UnknownHytaleUsers exist
     */
    public static function getLinkedHytaleUser(): HytaleUserList
    {
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->readObjects();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        $hytaleUserList = new HytaleUserList();
        if (empty($userToHytaleUserIDs)) {
            throw new Exception('No linked hytale User.', 400);
        }
        $hytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        return $hytaleUserList;
    }
}
