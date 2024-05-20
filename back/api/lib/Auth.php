<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 13/10/17
 * Time: 01:33 PM
 */

use Phalcon\Http\Request;
use Phalcon\Di;
use \Firebase\JWT\JWT;

class Auth
{
    public static function validateRequest(Request $request, string $jwt_key): bool
    {
        $di = \Phalcon\DI::getDefault();
        $headers = getallheaders();
        //Logger::log(print_r($headers, 1));
        if(isset($headers["authorization"]) || !empty($headers["authorization"])) {
            $headers["Authorization"] = $headers["authorization"];
        }
        if(!isset($headers["Authorization"]) || empty($headers["Authorization"]))
        {
            return false;
        }
        $secretKey = base64_decode($jwt_key);
        $jwt = explode(" ", $headers["Authorization"]);
        try {
            $decoded_token = JWT::decode($jwt[1], $secretKey, array('HS512'));
        } catch (Exception $e) {
            return false; // echo "error";
        }

        if (isset($decoded_token)) {
            if ($decoded_token->data->user_id > -1 && $decoded_token->data->user_id !== null) {
                $di->set('auth_token', $decoded_token);
                $di->set('user_id', $decoded_token->data->user_id);
                $di->set('account_id', $decoded_token->data->account_id);
                return true;
            }
        }
        return false;
    }

    public static function login(string $username, string $password): SysUsers
    {
        $user = SysUsers::findFirst(
            [
                "(email = :name:) AND (password = :password:)",
                //"(email = :name: OR nickname = :name:) AND (password = :password:)",
                "bind" => [
                    "name"    => $username,
                    "password" => sha1($password),
                ]
            ]
        );
        //Logger::log(print_r($user, 1));

        if ($user !== false) {
            $user->password_code = null;
            $user->update();

            return $user;
        }

        return -1;
    }

    public static function createToken(string $server_name, array $data, string $jwt_key): string
    {
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;         //Adding 10 seconds
        $expire = $notBefore + (60 * 60 * 30 * 24); // + 5 horas
        $serverName = $server_name;     // Retrieve the server name from config file

        /*
         * Create the token as an array
         */
        $raw_data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss' => $serverName,       // Issuer
            'nbf' => $notBefore,        // Not before
            'exp' => $expire,           // Expire
            'data' => $data
        ];

        $secretKey = base64_decode($jwt_key);

        $jwt = JWT::encode(
            $raw_data,      //Data to be encoded in the JWT
            $secretKey,     // The signing key
            'HS512'         // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return $jwt;
    }

    public static function getUserData(Phalcon\Config $config)
    {
        $headers = getallheaders();
        if(isset($headers["authorization"]) || !empty($headers["authorization"])) {
            $headers["Authorization"] = $headers["authorization"];
        }
        if(!isset($headers["Authorization"]) || empty($headers["Authorization"])) {
            return null;
        }
        $secretKey = base64_decode($config->jwtkey);
        $jwt = explode(" ", $headers["Authorization"]);
        $decoded_token = JWT::decode($jwt[1], $secretKey, array('HS512'));
        return $decoded_token->data;
    }

    public static function getUserRol(int $userId): string
    {
        $userRol = '';
        if ($userId > 0) {
            $grants = SysGrants::find(['user_id = ' . $userId]);
            if ($grants->count()) {
                foreach ($grants as $g) {
                    $rol = SysRoles::findFirst($g->role_id);
                    if ($rol !== false) {
                        $userRol = $rol->name;
                    }
                }
            }
        }
        return $userRol;
    }

    //aux obtener token descifrado
    public static function getTokenData(Phalcon\Config $config)
    {
        $headers = getallheaders();
        if(!isset($headers["Authorization"]) || empty($headers["Authorization"])) {
            return null;
        }
        $secretKey = base64_decode($config->jwtkey);
        $jwt = explode(" ", $headers["Authorization"]);
        $decoded_token = JWT::decode($jwt[1], $secretKey, array('HS512'));
        return $decoded_token;
    }
}
