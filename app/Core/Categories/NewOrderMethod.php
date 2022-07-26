<?php


namespace App\Core\Categories;


interface NewOrderMethod
{
    /**
     * @param array $data
     * @return mixed
     */
    public function setData(array $data);

    /**
     * @return mixed
     */
    public function addOrder();

    /**
     * @return mixed
     */
    public function editOrder();
}