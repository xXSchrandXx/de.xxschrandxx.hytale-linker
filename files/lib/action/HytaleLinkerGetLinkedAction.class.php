<?php

namespace wcf\action;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use wcf\util\HytaleLinkerUtil;

/**
 * AbstractHytaleLinker action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class HytaleLinkerGetLinkedAction extends AbstractHytaleGETAction
{
    /**
     * @inheritDoc
     */
    public $availableHytaleIDs = HYTALE_LINKER_IDENTITY;

    public function execute($parameters): JsonResponse
    {
        $hytaleUsers = [];
        try {
            $hytaleUserList = HytaleLinkerUtil::getLinkedHytaleUser();
            $hytaleUserList->readObjects();
            /** @var \wcf\data\user\hytale\HytaleUser[] */
            $hytaleUsers = $hytaleUserList->getObjects();
        } catch (Exception $e) {
            // Exception handled with empty check
        }
        if (empty($hytaleUsers)) {
            return $this->send('OK', 200, [
                'uuids' => []
            ]);
        }

        $uuids = [];
        foreach ($hytaleUsers as $hytaleUser) {
            \array_push($uuids, $hytaleUser->getHytaleUUID());
        }

        return $this->send('OK', 200, [
            'uuids' => $uuids
        ]);
    }
}
