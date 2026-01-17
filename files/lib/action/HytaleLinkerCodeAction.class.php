<?php

namespace wcf\action;

use BadMethodCallException;
use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\hytale\HytaleUserEditor;
use wcf\data\user\hytale\HytaleUserList;
use wcf\data\user\hytale\UserToHytaleUserList;

/**
 * HytaleLinkerCode action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
#[\wcf\http\attribute\DisableXsrfCheck]
class HytaleLinkerCodeAction extends AbstractHytaleLinkerAction
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['HYTALE_LINKER_ENABLED'];

    /**
     * @inheritDoc
     */
    public bool $ignoreName = false;

    /**
     * @inheritDoc
     */
    public $availableHytaleIDs = HYTALE_LINKER_IDENTITY;

    /**
     * @inheritdoc
     */
    public function execute($parameters): JsonResponse
    {
        // check edit
        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUUID = ?', [$parameters['uuid']]);
        $hytaleUserList->readObjects();
        try {
            /** @var \wcf\data\user\hytale\HytaleUser */
            $hytaleUser = $hytaleUserList->getSingleObject();
            if ($hytaleUser !== null) {
                // check linked
                $userToHytaleUserList = new UserToHytaleUserList();
                $userToHytaleUserList->getConditionBuilder()->add('hytaleUserID = ?', [$hytaleUser->getObjectID()]);
                if ($userToHytaleUserList->countObjects() !== 0) {
                    if (ENABLE_DEBUG_MODE) {
                        return $this->send('OK UUID already linked.', 200, ['code' => '']);
                    } else {
                        return $this->send('OK', 200, ['code' => '']);
                    }
                } else {
                    return $this->send('OK', 200, ['code' => $hytaleUser->getCode()]);
                }
            }
        } catch (BadMethodCallException $e) {
        }
        $code = bin2hex(\random_bytes(4));
        // create databaseobject
        HytaleUserEditor::create([
            'hytaleUUID' => $parameters['uuid'],
            'hytaleName' => $parameters['name'],
            'code' => $code
        ]);
        return $this->send('OK', 200, ['code' => $code]);
    }
}
