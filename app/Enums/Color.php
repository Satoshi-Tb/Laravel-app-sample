<?php

namespace App\Enums;

enum Color: string implements \App\Interfaces\Display
{
    case Red = '#FA777B';

    case Orange = '#E69D5A';

    case Yellow = '#E6DE56';

    case Blue = '#61A1E5';

    case Green = '#6CF082';

    case Purple = '#C1A3E5';

    public function display(): string
    {
        return match($this) {
            self::Red => '赤',
            self::Orange => 'オレンジ',
            self::Yellow => '黄',
            self::Blue => '青',
            self::Green => '緑',
            self::Purple => '紫',
        };
    }
}
