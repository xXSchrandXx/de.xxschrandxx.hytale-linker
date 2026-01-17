<?php

namespace wcf\page;

use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * HytaleUser list class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Page
 */
class HytaleUserListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['HYTALE_LINKER_ENABLED','HYTALE_LINKER_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.hytaleLinker.canManage'];

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $objectListClassName = HytaleUserList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'hytaleUserID';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public function show()
    {
        // set active tab
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.hytaleSection.hytaleUserList');

        parent::show();
    }

    /**
     * @inheritDoc
     */
    public function initObjectList()
    {
        parent::initObjectList();

        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->getUserID()]);
        $userToHytaleUserList->readObjectIDs();
        $userToHytaleUserIDs = $userToHytaleUserList->getObjectIDs();

        if (empty($userToHytaleUserIDs)) {
            $this->objectList->getConditionBuilder()->add('hytaleUserID IN (?)', [[0]]);
            return;
        }

        $this->objectList->getConditionBuilder()->add('hytaleUserID IN (?)', [$userToHytaleUserIDs]);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'showHytaleLinkerBranding' => true
        ]);
    }
}
