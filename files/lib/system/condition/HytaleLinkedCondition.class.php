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
 * HytaleLinker linked condition class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Condition
 */
class HytaleLinkedCondition extends AbstractCheckboxCondition implements IUserCondition, IObjectListCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $fieldName = 'hytaleLinked';

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

        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        if ($hytaleUserList->countObjects() === 0) {
            return false;
        } else {
            return true;
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
            $userToHytaleUserList = new UserToHytaleUserList();
            $userToHytaleUserList->readObjectIDs();
            $userToHytaleUserIDs = $userToHytaleUserList->readObjectIDs();
            if (empty($userToHytaleUserIDs)) {
                return;
            }
            $objectList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        }

        $objectList->readObjects();
    }
}
