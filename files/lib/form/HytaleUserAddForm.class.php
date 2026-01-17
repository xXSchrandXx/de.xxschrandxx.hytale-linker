<?php

namespace wcf\form;

use wcf\data\user\hytale\HytaleUser;
use wcf\data\user\hytale\HytaleUserEditor;
use wcf\data\user\hytale\UserToHytaleUser;
use wcf\data\user\hytale\UserToHytaleUserAction;
use wcf\data\user\hytale\UserToHytaleUserList;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;
use wcf\util\HytaleLinkerUtil;

/**
 * HytaleUser add form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Form
 */
class HytaleUserAddForm extends AbstractFormBuilderForm
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
    public $objectActionClass = UserToHytaleUserAction::class;

    /**
     * Weather maxReached should be shown
     */
    protected $showMaxReached = false;

    /**
     * Weather noUnknownUsers should be shown
     */
    protected $showNoUnknownUsers = false;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction === 'edit') {
            return;
        }

        $userToHytaleUserList = new UserToHytaleUserList();
        $userToHytaleUserList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->getUserID()]);

        $this->showMaxReached = (HYTALE_MAX_UUIDS == 0 || HYTALE_MAX_UUIDS <= $userToHytaleUserList->countObjects());
    }

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
    public function createForm()
    {
        parent::createForm();

        if ($this->formAction === 'edit') {
            return;
        }

        $this->readOptions();

        $this->showNoUnknownUsers = empty($this->options);

        if ($this->showNoUnknownUsers) {
            $this->form->addDefaultButton(false);
            return;
        }

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create()
                        ->required()
                        ->maximumLength(30)
                        ->value('Default')
                        ->available(HYTALE_MAX_UUIDS > 1),
                    SingleSelectionFormField::create('hytaleUserID')
                        ->required()
                        ->label('wcf.form.hytaleUserAdd.hytaleUserID')
                        ->description('wcf.form.hytaleUserAdd.hytaleUserID.description')
                        ->options(
                            $this->options,
                            true,
                            false
                        )
                        ->filterable(),
                    TextFormField::create('code')
                        ->required()
                        ->label('wcf.form.hytaleUserAdd.code')
                        ->description('wcf.form.hytaleUserAdd.code.description')
                        ->addValidator(new FormFieldValidator('checkCode', function (TextFormField $field) {
                            $hytaleUserID = $this->form->getData()['data']['hytaleUserID'];
                            if ($hytaleUserID === null) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'noValidSelection'
                                    )
                                );
                                return;
                            }
                            $hytaleUser = new HytaleUser($hytaleUserID);
                            if ($hytaleUser->getObjectID() === 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'noValidSelection'
                                    )
                                );
                                return;
                            }
                            $userToHytaleUser = new UserToHytaleUser($hytaleUser->getObjectID());
                            if ($userToHytaleUser->getObjectID() !== 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'alreadyUsed',
                                        'wcf.form.hytaleUserAdd.code.error.alreadyUsed'
                                    )
                                );
                                return;
                            }
                            if (!hash_equals($hytaleUser->getCode(), $field->getValue())) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'wrongSecurityCode',
                                        'wcf.form.hytaleUserAdd.code.error.wrongSecurityCode'
                                    )
                                );
                            }
                        }))
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction === 'edit') {
            parent::save();
            return;
        }

        $this->additionalFields['userID'] = WCF::getUser()->getUserID();

        $title = 'Default';
        if (isset($this->form->getData()['data']['title'])) {
            $title = $this->form->getData()['data']['title'];
        }

        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'title',
                true
            )
        );
        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'code',
                true
            )
        );

        $hytaleUser = new HytaleUser($this->form->getData()['data']['hytaleUserID']);
        $editor = new HytaleUserEditor($hytaleUser);
        $editor->update([
            'title' => $title,
            'createdDate' => \TIME_NOW
        ]);

        parent::save();
    }

    /**
     * Unlinked uuids
     * @var array
     */
    protected $options;

    /**
     * Lists unlinked uuids
     */
    protected function readOptions()
    {
        $this->options = [];

        $hytaleUserList = HytaleLinkerUtil::getUnlinkedHytaleUser();
        $hytaleUserList->readObjects();
        /** @var HytaleUser[] */
        $hytaleUsers = $hytaleUserList->getObjects();

        foreach ($hytaleUsers as $hytaleUserID => $hytaleUser) {
            \array_push($this->options, ['label' => $hytaleUser->getHytaleName(), 'value' => $hytaleUserID, 'depth' => 0]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'showMaxReached' => $this->showMaxReached,
            'showNoUnknownUsers' => $this->showNoUnknownUsers,
            'showHytaleLinkerBranding' => true
        ]);
    }
}
