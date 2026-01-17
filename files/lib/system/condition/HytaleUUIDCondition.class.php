<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\UserList;
use wcf\data\user\User;
use wcf\data\DatabaseObjectList;
use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\system\WCF;

/**
 * HytaleLinker uuid condition class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Condition
 */
class HytaleUUIDCondition extends AbstractTextCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $fieldName = 'hytaleUUID';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.user.condition.hytaleLinker.isLinked';

    /**
     * @inheritDoc
     */
    protected function getLabel()
    {
        return WCF::getLanguage()->get($this->label);
    }

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$user->getUserID()]);
        $userToHytaleUserList->readObjectIDs();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        if (empty($userToHytaleUserIDs)) {
            return false;
        }

        $userHytaleList = new HytaleUserList();
        $userHytaleList->getConditionBuilder()->add('hytaleUserID IN (?) AND hytaleUUID = ?', [$userToHytaleUserIDs, $this->fieldValue]);

        if ($userHytaleList->countObjects() === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new \InvalidArgumentException("Object list is no instance of '" . UserList::class . "', instance of '" . get_class($objectList) . "' given.");
        }

        if (isset($conditionData[$this->fieldName]) && $conditionData[$this->fieldName]) {
            $hytaleUserList = new HytaleUserList();
            $hytaleUserList->getConditionBuilder()->add('hytaleUUID = ?', $conditionData[$this->fieldName]);
            $hytaleUserList->readObjectIDs();

            $hytaleUserIDs = $hytaleUserList->getObjectIDs();
            if (empty($hytaleUserIDs)) {
                return;
            }
            $userToHytaleUserList = new UserToHytaleUserList();
            $objectList->getConditionBuilder()->add('hytaleUserID IN (?)', [$hytaleUserIDs]);
            $userToHytaleUserList->readObjectIDs();
            $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();
            if (empty($userToHytaleUserIDs)) {
                return;
            }
            $objectList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        }

        $objectList->readObjects();
    }
}
