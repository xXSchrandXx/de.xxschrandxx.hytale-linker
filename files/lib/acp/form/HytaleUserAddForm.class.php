<?php

namespace wcf\acp\form;

use wcf\data\user\hytale\HytaleUser;
use wcf\data\user\User;
use wcf\data\user\hytale\HytaleUserAction;
use wcf\data\user\hytale\HytaleUserEditor;
use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUser;
use wcf\data\user\hytale\UserToHytaleUserAction;
use wcf\data\user\hytale\UserToHytaleUserEditor;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HytaleLinkerUtil;

/**
 * HytaleUser add via text acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class HytaleUserAddForm extends AbstractFormBuilderForm
{
    /**
     * @var \wcf\data\user\hytale\HytaleUser
     */
    public $formObject;

    /**
     * @inheritDoc
     */
    public $neededModules = ['HYTALE_LINKER_ENABLED','HYTALE_LINKER_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.hytaleLinker.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.management';

    /**
     * @inheritDoc
     */
    public $objectActionClass = HytaleUserAction::class;

    /**
     * Benutzer-Objekt
     * @var User|null
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction == 'create') {
            $userID = 0;
            if (isset($_REQUEST['id'])) {
                $userID = (int)$_REQUEST['id'];
            }
            $this->user = new User($userID);
            if (!$this->user->getUserID()) {
                throw new IllegalLinkException();
            }
        } else {
            $hytaleUserID = 0;
            if (isset($_REQUEST['id'])) {
                $hytaleUserID = (int)$_REQUEST['id'];
            }
            $this->formObject = new HytaleUser($hytaleUserID);
            if (!$this->formObject->hytaleUserID) {
                throw new IllegalLinkException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function checkModules()
    {
        parent::checkModules();

        if (!(HYTALE_LINKER_ENABLED && HYTALE_LINKER_IDENTITY)) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create()
                        ->required()
                        ->maximumLength(30)
                        ->value('Default'),
                    TextFormField::create('hytaleUUID')
                        ->required()
                        ->label('wcf.acp.form.hytaleUserAdd.hytaleUUID')
                        ->description('wcf.acp.form.hytaleUserAdd.hytaleUUID.description')
                        ->minimumLength(36)
                        ->maximumLength(36)
                        ->pattern(HytaleLinkerUtil::UUID_PATTERN)
                        ->placeholder('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')
                        ->addValidator(new FormFieldValidator('checkHytaleUser', function (TextFormField $field) {
                            if ($this->formAction == 'edit') {
                                if ($field->getValue() == $this->formObject->getHytaleUUID()) {
                                    return;
                                }
                            }
                            $hytaleUserList = new HytaleUserList();
                            $hytaleUserList->getConditionBuilder()->add('hytaleUUID = ?', [$field->getValue()]);
                            if ($hytaleUserList->countObjects() == 0) {
                                return;
                            }
                            $userToHytaleUserList = new UserToHytaleUserList();
                            $hytaleUserList->readObjectIDs();
                            $userToHytaleUserList->getConditionBuilder()->add('hytaleUserID IN (?)', [$hytaleUserList->getObjectIDs()]);
                            if ($userToHytaleUserList->countObjects() == 0) {
                                $hytaleUserList->readObjects();
                                $hytaleUserEditor = new HytaleUserEditor($hytaleUserList->getSingleObject());
                                $hytaleUserEditor->delete();
                                return;
                            }
                            $field->addValidationError(
                                new FormFieldValidationError('alreadyUsed', 'wcf.acp.form.hytaleUserAdd.hytaleUUID.error.alreadyUsed')
                            );
                        })),
                    TextFormField::create('hytaleName')
                        ->label('wcf.acp.form.hytaleUserAdd.hytaleName')
                        ->description('wcf.acp.form.hytaleUserAdd.hytaleName.description')
                        ->minimumLength(3)
                        ->maximumLength(16)
                        ->pattern('[0-9a-fA-F_]{3-16}')
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['code'] = '';
            $this->additionalFields['createdDate'] = \TIME_NOW;
        }

        parent::save();
    }

    /**
     * @inheritDoc
     */
    public function saved()
    {
        if ($this->formAction == 'create') {
            $data = [
                ['data'] => [
                    'userID' => $this->user->getUserID(),
                    'hytaleUserID' => $this->objectAction->getReturnValues()['returnValues']->getObjectID()
                ]
            ];
            (new UserToHytaleUserAction([], 'create', $data))->executeAction();
        }

        parent::saved();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        if ($this->formAction == 'create') {
            WCF::getTPL()->assign([
               'userID' => $this->user->getUserID()
            ]);
        } else {
            $userToHytaleUser = new UserToHytaleUser($this->formObject->getObjectID());
            WCF::getTPL()->assign([
                'userID' => $userToHytaleUser->getUserID()
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        if ($this->formAction == 'create') {
            $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, ['id' => $this->user->getUserID()]));
        } else {
            parent::setFormAction();
        }
    }
}
