<?php

namespace App\Exceptions;

use Exception;
use DomainException;


class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }
    /**
     * Thrown when we're unable to communicate with smsc.ru.
     *
     * @param  DomainException  $exception
     *
     * @return static
     */
    public static function smscRespondedWithAnError(DomainException $exception)
    {
        return new static(
            "smsc.ru responded with an error '{$exception->getCode()}: {$exception->getMessage()}'",
            $exception->getCode(),
            $exception
        );
    }
    /**
     * Thrown when we're unable to communicate with smsc.ru.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithSmsc(Exception $exception)
    {
        return new static(
            "The communication with smsc.ru failed. Reason: {$exception->getMessage()}".
            $exception->getCode(),
            $exception
        );
    }
}