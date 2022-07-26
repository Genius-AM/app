<?php


namespace App\Core\Categories;


interface ChangeAmountMethod
{
    /**
     * @param array $data
     * @return mixed
     */
    public function setData(array $data);

    /**
     * @return bool
     */
    public function changeAmount(): bool;
}