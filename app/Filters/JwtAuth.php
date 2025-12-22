<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Auth;

class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('api_response');
        
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            return responseError(401, 'Please authenticate');
        }

        try {
            $config = new Auth();
            $decoded = JWT::decode($token, new Key($config->jwtSecret, 'HS256'));
            
            // Check Token Type
            if (!isset($decoded->type) || $decoded->type !== $config->tokenTypes['ACCESS']) {
                throw new \Exception('Invalid token type');
            }

            // Attach user ID to the request object for Controllers to use
            $request->user = (object)['id' => $decoded->sub];

        } catch (\Exception $e) {
            return responseError(401, 'Please authenticate');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}