<?php

namespace App\Services\Constant;

use DateTime;
use DateTimeImmutable;

class DateConstant
{
    public const DATE_YEAR_TIME_RU = 'j F Y';

    /**
     * @param DateTime|DateTimeImmutable $date
     *
     * @return string
     */
    public function getDateWithYearOrNot($date): string
    {
        $dateStr = $date->format(self::DATE_YEAR_TIME_RU);

        return mb_strtolower($this->parseEnDateToRu($dateStr));
    }

    public function parseEnDateToRu(string $date): string
    {
        $ru_month = array( 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря' );
        $en_month = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

        return str_replace($en_month, $ru_month, $date);
    }
}