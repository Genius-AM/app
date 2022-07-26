<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Class HttpClient
 *
 * @method static \Psr\Http\Message\ResponseInterface get(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface head(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface put(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface post(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface patch(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 * @method static \Psr\Http\Message\ResponseInterface delete(string|\Psr\Http\Message\UriInterface $uri, array $options = [])
 *
 * @mixin \GuzzleHttp\Client
 */
class HttpClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'HttpClient';
    }
}