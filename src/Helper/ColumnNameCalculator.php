<?php

declare(strict_types=1);

namespace Xml\Processor\Helper;

class ColumnNameCalculator
{
    public static function getColumnNameFromNumber(int $num): string
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric); //65 === A
        $num2 = (int)(($num - 1) / 26);
        if ($num2 > 0) {
            return self::getColumnNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
}
