<?php


namespace App\Core\Notifications;

use App\Facades\HttpClient;
use App\User;
use App\UsersDeviceToken;
use Illuminate\Support\Facades\Config;

class PushNotification
{
    /**
     * @var mixed
     */
    private $key;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var array
     */
    private $notificData;

    /**
     * @var
     */
    private $fcmNotification;


    public function __construct()
    {
        $this->key = Config::get('constants.firebase_token');
        $this->client = app('HttpClient', [
            'headers' => [
                'Authorization' => 'key=' . $this->key,
                'Content-Type'  =>  'application/json'
            ],
            'verify' => false
        ]);
    }

    /**
     * @param User $user
     * @return PushNotification
     */
    public static function to(User $user) : PushNotification
    {
        $service = new static();
        $service->toUser($user);

        return $service;
    }

    /**
     * @param User $user
     * @return PushNotification
     */
    public function toUser(User $user): PushNotification
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Отправка уведомления пользователю
     *
     * @param $title
     * @param $description
     * @return bool
     */
    public function send($title, $description)
    {
        /** @var UsersDeviceToken $value */
        foreach($this->user->deviceToken()->get() as $key => $value) {
            if (empty($value->device_ios) && empty($value->device_android)) continue;
            if ($value->device_ios && $value->device_android) continue;
            $this->setData($title, $description, $value);
            $this->setFields($value);

            $responce = $this->client->post($this->fcmUrl, ['json' => $this->fcmNotification]);
        }

        return true;
    }

    /**
     * @param $title
     * @param $description
     * @param UsersDeviceToken $value
     */
    private function setData($title, $description, UsersDeviceToken $value)
    {
        if ($value->device_ios == 1) {
            $this->notificData = ['title' => $title, "body" => $description];
        }

        if ($value->device_android == 1) {
            $this->notificData = ['title' => $title, "message" => $description];
        }
    }

    /**
     * @param UsersDeviceToken $value
     */
    private function setFields(UsersDeviceToken $value)
    {
        if ($value->device_ios == 1) {
            $this->fcmNotification = [
                'to'                => $value->token,
                'notification'      => $this->notificData
            ];
        }
        if ($value->device_android == 1) {
            $this->fcmNotification = [
                'to'                => $value->token,
                'data'              => $this->notificData
            ];
        }
    }
}