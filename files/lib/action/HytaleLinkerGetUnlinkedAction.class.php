<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\util\HytaleLinkerUtil;

/**
 * AbstractHytaleLinker action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class HytaleLinkerGetUnlinkedAction extends AbstractHytaleGETAction
{
    /**
     * @inheritDoc
     */
    public $availableHytaleIDs = HYTALE_LINKER_IDENTITY;

    public function execute($parameters): JsonResponse
    {
        $hytaleUserList = HytaleLinkerUtil::getUnlinkedHytaleUser();
        $hytaleUserList->readObjects();
        /** @var \wcf\data\user\hytale\HytaleUser[] */
        $hytaleUsers = $hytaleUserList->getObjects();
        if (empty($hytaleUsers)) {
            return $this->send('OK', 200, [
                'uuids' => []
            ]);
        }

        $uuids = [];
        foreach ($hytaleUsers as $hytaleUser) {
            $uuids[$hytaleUser->getHytaleUUID()] = $hytaleUser->getCode();
        }

        return $this->send('OK', 200, [
            'uuids' => $uuids
        ]);
    }
}
