<?php

namespace wcf\data\user\hytale;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;

/**
 * HytaleUser Data class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Hytale
 *
 * @property-read int $hytaleUserID
 * @property-read string $title
 * @property-read string $hytaleUUID
 * @property-read string $hytaleName
 * @property-read string $code
 * @property-read int $createdDate
 */
class HytaleUser extends DatabaseObject implements ITitledObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'user_hytale';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'hytaleUserID';

     /**
      * @inheritDoc
      */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns Hytale-UUID
     * @return ?string
     */
    public function getHytaleUUID()
    {
        return $this->hytaleUUID;
    }

    /**
     * Returns Hytale-Name
     * @return ?string
     */
    public function getHytaleName()
    {
        return $this->hytaleName;
    }

    /**
     * Returns code
     * @return ?string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns createdTimestamp
     * @return ?int
     */
    public function getCreatdDate()
    {
        return $this->createdDate;
    }
}
