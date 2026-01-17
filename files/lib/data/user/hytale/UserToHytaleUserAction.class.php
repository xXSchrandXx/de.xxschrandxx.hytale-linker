<?php

namespace wcf\data\user\hytale;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\util\UserRegistrationUtil;

/**
 * UserToHytaleUser Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class UserToHytaleUserAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = UserToHytaleUserEditor::class;

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
     * @return \wcf\data\user\hytale\UserToHytaleUser
     */
    public function create()
    {
        /** @var \wcf\data\user\hytale\UserToHytaleUser */
        $userToHytaleUser = parent::create();
        if (!HYTALE_ENABLE_ACTIVE_USER) {
            return $userToHytaleUser;
        }

        $user = new User($userToHytaleUser->getUserID());
        // skip not existing user
        if (!$user->userID) {
            return $userToHytaleUser;
        }

        // skip activated user
        if (!$user->pendingActivation()) {
            return $userToHytaleUser;
        }

        // activate user
        $action = new UserAction([$user], 'enable');
        $action->executeAction();

        return $userToHytaleUser;
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if (!HYTALE_ENABLE_DISABLE_USER) {
            return parent::delete();
        }
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // deactivate users without link
        $users = [];
        /** @var \wcf\data\user\hytale\UserToHytaleUser $object */
        foreach ($this->getObjects() as $userToHytaleUser) {
            $user = new User($userToHytaleUser->userID);
            // skip not existing user
            if (!$user->userID) {
                continue;
            }
            // skip admins
            if ($user->hasAdministrativeAccess()) {
                continue;
            }
            // check weather mandatory
            $userProfile = new UserProfile($user);
            if ($userProfile->getPermission('user.hytaleLinker.mandatory') != 1) {
                continue;
            }
            // check weather last userToHytaleUser
            $userToHytaleUserList = new UserToHytaleUserList();
            $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [$userToHytaleUser->userID]);
            if ($userToHytaleUserList->countObjects() > 1) {
                continue;
            }

            $users[] = $user;
        }
        // disable users
        if (!empty($users)) {
            $action = new UserAction($users, 'update', [
                'data' => [
                    'activationCode' => UserRegistrationUtil::getActivationCode()
                ],
                'removeGroups' => UserGroup::getGroupIDsByType([UserGroup::USERS]),
            ]);
            $action->executeAction();
            $action = new UserAction($users, 'addToGroups', [
                'groups' => UserGroup::getGroupIDsByType([UserGroup::GUESTS]),
                'deleteOldGroups' => false,
                'addDefaultGroups' => false
            ]);
            $action->executeAction();
        }

        return parent::delete();
    }
}
