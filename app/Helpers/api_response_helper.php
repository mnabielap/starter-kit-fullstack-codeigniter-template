<?php

use CodeIgniter\HTTP\ResponseInterface;

if (!function_exists('responseSuccess')) {
    /**
     * Send a formatted success JSON response.
     * 
     * @param mixed $data The payload to return
     * @param int $statusCode HTTP Status code (default 200)
     * @param string $message Optional message (used if data is null)
     */
    function responseSuccess($data = null, int $statusCode = 200, string $message = 'Success'): ResponseInterface
    {
        $response = service('response');
        
        // 204 No Content should not have a body
        if ($statusCode === 204) {
            return $response->setStatusCode($statusCode);
        }

        $body = $data ?? ['message' => $message];

        return $response->setJSON($body)->setStatusCode($statusCode);
    }
}

if (!function_exists('responseError')) {
    /**
     * Send a formatted error JSON response.
     * 
     * @param int $statusCode HTTP Status code
     * @param string $message Error message
     * @param mixed $errors Detailed validation errors
     */
    function responseError(int $statusCode, string $message, $errors = null): ResponseInterface
    {
        $response = service('response');

        $body = [
            'code'    => $statusCode,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return $response->setJSON($body)->setStatusCode($statusCode);
    }
}