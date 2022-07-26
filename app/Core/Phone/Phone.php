<?php


namespace App\Core\Phone;


class Phone
{
    /**
     * Проверяем первый символ + ли, если нет, то добавляем его в начало
     *
     * @param $phone
     * @return string
     */
    static public function checkOrAddPlusOnNumber($phone)
    {
        if (!empty($phone)) {
            $phone = trim($phone);
            $first_letter = substr($phone, 0, 1);
            if ($first_letter !== "+") {
                $phone = "+".$phone;
            }
            return $phone;
        } else {
            return "";
        }
    }
}