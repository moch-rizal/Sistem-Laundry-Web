<?php 
namespace App\Filters; 

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;  
use CodeIgniter\HTTP\ResponseInterface;   
use Firebase\JWT\JWT;                      
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine("Authorization");
        $token = null;

        // Ekstrak token dari header
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        // Jika token tidak ada, tolak akses
        if ($token === null) {
            $response = service('response');
            $response->setStatusCode(401);
            $response->setJSON(['status' => 'error', 'message' => 'Akses ditolak: token tidak disediakan.']);
            return $response;
        }

        try {
            // Dekode token
            $key = $_ENV['JWT_SECRET_KEY'];
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Simpan data user dari token ke dalam request agar bisa diakses di controller
            $request->user = $decoded;

        } catch (ExpiredException $e) {
            // Tangani jika token sudah kedaluwarsa
            $response = service('response');
            $response->setStatusCode(401);
            $response->setJSON(['status' => 'error', 'message' => 'Token sudah kedaluwarsa.']);
            return $response;
        } catch (\Exception $e) {
            // Tangani jika token tidak valid
            $response = service('response');
            $response->setStatusCode(401);
            $response->setJSON(['status' => 'error', 'message' => 'Token tidak valid.']);
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}