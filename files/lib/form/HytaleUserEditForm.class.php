<?php

namespace wcf\form;

use wcf\data\user\hytale\HytaleUser;
use wcf\data\user\hytale\HytaleUserAction;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;

/**
 * HytaleUser edit form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Form
 */
class HytaleUserEditForm extends HytaleUserAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public $objectActionClass = HytaleUserAction::class;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (HYTALE_MAX_UUIDS <= 1) {
            throw new IllegalLinkException();
        }

        $hytaleUserID = 0;
        if (isset($_REQUEST['id'])) {
            $hytaleUserID = (int)$_REQUEST['id'];
        }
        $this->formObject = new HytaleUser($hytaleUserID);
        if (!$this->formObject->getObjectID()) {
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
                ->appendChild(
                    TitleFormField::create()
                        ->required()
                        ->maximumLength(30)
                        ->value('Default')
                        ->available(HYTALE_MAX_UUIDS > 1)
                )
        );
    }
}
