<?php

namespace wcf\system\event\listener;

use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\system\WCF;

/**
 * HytaleUser acp edit listener class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\System\Event\Listener
 */
class HytaleACPUserEditListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (!(HYTALE_LINKER_ENABLED && HYTALE_LINKER_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.hytaleLinker.canManage')) {
            return;
        }

        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$_REQUEST['id']]);
        $userToHytaleUserList->readObjectIDs();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        if (empty($userToHytaleUserIDs)) {
            WCF::getTPL()->assign([
                'hytaleUsers' => []
            ]);
            return;
        }

        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
        $hytaleUserList->readObjects();
        /** @var \wcf\data\user\hytale\HytaleUser[] */
        $hytaleUsers = $hytaleUserList->getObjects();

        WCF::getTPL()->assign([
            'hytaleUsers' => $hytaleUsers
        ]);
    }
}
