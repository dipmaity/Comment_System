// CorsMiddleware.php

namespace YourNamespace;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class CorsMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        // Handle CORS preflight request
        if ($request->getMethod() === 'OPTIONS') {
            $response = (new ResponseFactory())->createResponse();
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Content-Type', 'application/json');

            return $response->withStatus(200);
        }

        // Handle CORS for regular requests
        $response = $next($request, $response);
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Content-Type', 'application/json');

        return $response;
    }
}
