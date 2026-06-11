<?php

class Menu
{
    private static ?array $items = null;

    public static function items(): array
    {
        if (self::$items === null) {
            self::$items = require __DIR__ . '/Menu/items.php';
        }

        return self::$items;
    }
}
