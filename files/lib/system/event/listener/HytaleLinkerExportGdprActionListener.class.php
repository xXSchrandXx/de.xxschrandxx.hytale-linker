<?php

namespace wcf\system\event\listener;

use wcf\acp\action\UserExportGdprAction;
use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;

class HytaleLinkerExportGdprActionListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param UserExportGdprAction $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $user = $eventObj->user;

        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$user->getUserID()]);
        $userToHytaleUserList->readObjectIDs();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        if (empty($userToHytaleUserIDs)) {
            $eventObj->data['de.xxschrandxx.wsc.hytale-linker'] = [];
            return;
        }

        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        $hytaleUserList->readObjects();
        /** @var \wcf\data\user\hytale\HytaleUser[] */
        $hytaleUsers = $hytaleUserList->getObjects();

        $hytaleLinkerData = [];
        foreach ($hytaleUsers as $hytaleUser) {
            $hytaleLinkerData[] = [
                'title' => $hytaleUser->getTitle(),
                'uuid' => $hytaleUser->getHytaleUUID(),
                'name' => $hytaleUser->getHytaleName(),
                'time' => $hytaleUser->getCreatdDate()
            ];
        }

        $eventObj->data['de.xxschrandxx.wsc.hytale-linker'] = $hytaleLinkerData;
    }
}
