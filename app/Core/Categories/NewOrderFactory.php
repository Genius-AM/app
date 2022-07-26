<?php


namespace App\Core\Categories;


use App\Category;
use App\Core\Categories\Quadbike\DivingNewOrder;
use App\Core\Categories\Quadbike\QuadbikeNewOrder;
use App\Core\Categories\Sea\SeaNewOrder;
use App\User;

class NewOrderFactory
{
    /**
     * @param int $id
     * @param array $array
     * @param User $user
     * @return NewOrderMethod
     * @throws \Exception
     */
    public static function getChangeMethod(int $id, array $array, User $user) : NewOrderMethod
    {
        switch ($id) {
            case Category::DIVING:
                return new DivingNewOrder($array, $user);
            case Category::QUADBIKE:
                return new QuadbikeNewOrder($array, $user);
            case Category::SEA:
                return new SeaNewOrder($array, $user);
            default:
                throw new \Exception("Unknown");
        }
    }
}