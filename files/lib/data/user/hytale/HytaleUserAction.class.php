<?php

namespace wcf\data\user\hytale;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * HytaleUser Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class HytaleUserAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = HytaleUserEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['user.hytaleLinker.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['user.hytaleLinker.canManage'];

    /**
     * @inheritDoc
     */
    public function delete()
    {
        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$this->getObjectIDs()]);
        $userToHytaleUserList->readObjects();
        $userToHytaleUsers = $userToHytaleUserList->getObjects();
        (new UserToHytaleUserAction($userToHytaleUsers, 'delete'))->executeAction();

        return parent::delete();
    }
}
