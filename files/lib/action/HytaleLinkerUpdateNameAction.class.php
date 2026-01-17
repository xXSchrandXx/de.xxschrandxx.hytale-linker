<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\user\hytale\HytaleUserEditor;
use wcf\data\user\hytale\HytaleUserList;

/**
 * HytaleLinkerUpdateName action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
#[\wcf\http\attribute\DisableXsrfCheck]
class HytaleLinkerUpdateNameAction extends AbstractMultipleHytaleLinkerAction
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
        // read hytaleUsers
        $hytaleUserList = new HytaleUserList();
        $hytaleUserList->getConditionBuilder()->add('hytaleUUID IN (?)', [array_keys($parameters['uuids'])]);
        if ($hytaleUserList->countObjects() === 0) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Unknown UUIDs', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        $hytaleUserList->readObjects();
        /** @var \wcf\data\user\hytale\HytaleUser[] */
        $hytaleUsers = $hytaleUserList->getObjects();

        foreach ($hytaleUsers as $hytaleUser) {
            if (!array_key_exists($hytaleUser->getHytaleUUID(), $parameters['uuids'])) {
                // Would never happen
                continue;
            }
            if (empty($parameters['uuids'][$hytaleUser->getHytaleUUID()])) {
                // Would never happen
                continue;
            }
            if (!array_key_exists('name', $parameters['uuids'][$hytaleUser->getHytaleUUID()])) {
                continue;
            }
            if (empty($parameters['uuids'][$hytaleUser->getHytaleUUID()]['name'])) {
                continue;
            }
            if ($hytaleUser->getHytaleName() === $parameters['uuids'][$hytaleUser->getHytaleUUID()]['name']) {
                continue;
            }
            $hytaleUserEditor = new HytaleUserEditor($hytaleUser);
            $hytaleUserEditor->update([
                'hytaleName' => $parameters['uuids'][$hytaleUser->getHytaleUUID()]['name']
            ]);
        }

        return $this->send();
    }
}
