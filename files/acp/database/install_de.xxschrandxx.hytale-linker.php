<?php

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;

return [
    // wcf1_user_hytale
    DatabaseTable::create('wcf1_user_hytale')
        ->columns([
            ObjectIdDatabaseTableColumn::create('hytaleUserID'),
            VarcharDatabaseTableColumn::create('title')
                ->length(16),
            VarcharDatabaseTableColumn::create('hytaleUUID')
                ->length(36)
                ->notNull(),
            VarcharDatabaseTableColumn::create('hytaleName')
                ->length(16)
                ->notNull(),
            VarcharDatabaseTableColumn::create('code')
                ->length(16)
                ->notNull(),
            IntDatabaseTableColumn::create('createdDate')
                ->length(10),
        ]),

    // wcf1_user_to_user_hytale
    DatabaseTable::create('wcf1_user_to_user_hytale')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('userID'),
            ObjectIdDatabaseTableColumn::create('hytaleUserID')
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['userID'])
                ->referencedTable('wcf1_user'),
            DatabaseTableForeignKey::create()
                ->columns(['hytaleUserID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['hytaleUserID'])
                ->referencedTable('wcf1_user_hytale')
        ])
];
