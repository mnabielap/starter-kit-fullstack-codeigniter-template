<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RateLimiter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api_response');
        $throttler = Services::throttler();

        // Limit: 60 requests per minute per IP
        if ($throttler->check(md5($request->getIPAddress()), 60, 60) === false) {
            return responseError(429, 'Too many requests, please try again later.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}