<?php


namespace App\Core\Categories\Quadbike;


use App\Core\Categories\NewOrderMethod;
use App\Route;
use App\User;
use Illuminate\Support\Carbon;

class DivingNewOrder implements NewOrderMethod
{
    private $data = [];

    public function __construct($array, User $user)
    {
        $this->setData($array);
    }

    public function setData(array $array)
    {
        if ($array) {
            $this->data = $array;
        }
    }

    public function addOrder()
    {
        
    }

    public function editOrder()
    {

    }
}