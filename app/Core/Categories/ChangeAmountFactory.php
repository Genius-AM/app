<?php


namespace App\Core\Categories;


use App\Category;
use App\Core\Categories\Diving\DivingChangeAmount;
use App\Core\Categories\Djipping\DjippingChangeAmount;
use App\Core\Categories\Quadbike\QuadbikeChangeAmount;
use App\Core\Categories\Sea\SeaChangeAmount;
use App\Models\Company;

class ChangeAmountFactory
{
    /**
     * @param int $id
     * @param array $array
     * @param Company $company
     * @return ChangeAmountMethod
     * @throws \Exception
     */
    public static function getChangeMethod(int $id, array $array, Company $company = null) : ChangeAmountMethod
    {
        switch ($id) {
            case Category::DJIPPING:
                return new DjippingChangeAmount($array);
            case Category::DIVING:
                return new DivingChangeAmount($array);
            case Category::QUADBIKE:
                return new QuadbikeChangeAmount($array, $company);
            case Category::SEA:
                return new SeaChangeAmount($array);
            default:
                throw new \Exception("Unknown");
        }
    }
}