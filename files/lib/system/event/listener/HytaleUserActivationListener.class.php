<?php

namespace wcf\system\event\listener;

use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\data\user\UserAction;

class HytaleUserActivationListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param UserAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if ($eventObj->getActionName() != 'enable') {
            return;
        }
        $objects = $eventObj->getObjects();

        foreach ($objects as $userEditor) {
            // check weather user is linked
            $userToHytaleUserList = new UserToHytaleUserList();
            $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$userEditor->getObjectID()]);
            if ($userToHytaleUserList->countObjects() >= 1) {
                continue;
            }
            // do not enable user
            unset($userEditor);
        }
        $eventObj->setObjects($objects);
    }
}
