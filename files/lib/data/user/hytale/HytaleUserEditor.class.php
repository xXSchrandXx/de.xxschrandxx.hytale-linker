<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObjectEditor;

/**
 * HytaleUser Editor class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 */
class HytaleUserEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = HytaleUser::class;
}
