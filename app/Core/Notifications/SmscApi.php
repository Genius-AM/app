<?php

namespace App\Core\Notifications;


use App\Core\Notifications\PushNotification;
use App\Exceptions\CouldNotSendNotification;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SmscApi
{
    const FORMAT_JSON = 3;

    /**
     * @var Client
     */
    protected $client;

    /** @var string */
    protected $endpoint = 'https://smsc.ru/sys/send.php';

    /** @var string */
    protected $login;

    /** @var string */
    protected $password;

    /** @var string */
    protected $sender;

    /** @var string */
    protected $phone;

    /**
     * @var
     */
    protected $user;

    /** @var string */
    protected $message;

    public function __construct()
    {
        $this->login = config('services.smscru.login');
        $this->password = config('services.smscru.password');
        $this->sender = config('services.smscru.sender');

        $this->client = new Client([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * @return $this
     */
    public static function create(): SmscApi
    {
        return new static();
    }

    /**
     * @param $value
     * @return $this
     */
    public function phone($value): SmscApi
    {
        $this->phone = $value;

        return $this;
    }

    /**
     * @param $user
     * @return SmscApi
     */
    public static function to($user) : SmscApi
    {
        $service = new static();
        $service->toUser($user);
        $service->setPhone();

        return $service;
    }

    /**
     * @param $user
     * @return SmscApi
     */
    public function toUser($user): SmscApi
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return SmscApi
     */
    public function setPhone() : SmscApi
    {
        if ($this->user instanceof \App\Client) {
            $this->phone = $this->user->phone;
        }
        if ($this->user instanceof \App\User) {
            $this->phone = $this->user->phone;
        }

        return $this;
    }

    /**
     * @param $message
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($message) : bool
    {
        $base = [
            'charset' => 'utf-8',
            'login'   => $this->login,
            'phones' => $this->phone,
            'mes'     => $message,
            'psw'     => $this->password,
//            'sender'  => $this->sender,
            'fmt'     => self::FORMAT_JSON,
        ];

        if (App::environment('production')) {
            $response = $this->client->request('POST', $this->endpoint, ['form_params' => $base]);
        }

        return true;
    }
}