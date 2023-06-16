<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
* @OA\Post(
*     path="/login",
*     description="Login to the system",
*     tags={"user"},
*     @OA\RequestBody(description="Basic user info", required=true,
*       @OA\MediaType(mediaType="application/json",
*    			@OA\Schema(
*    				@OA\Property(property="email", type="string", example="dino.keco@gmail.com",	description="Email"),
*    				@OA\Property(property="password", type="string", example="1234",	description="Password" )
*        )
*     )),
*     @OA\Response(
*         response=200,
*         description="JWT Token on successful response"
*     ),
*     @OA\Response(
*         response=404,
*         description="Wrong Password | User doesn't exist"
*     )
* )
*/
Flight::route('POST /login', function () {
    $login = Flight::request()->data->getData();
    $user = Flight::userDao()->get_user_by_email($login['email']);
    if (isset($user['id'])) {
        if (password_verify($login['password'], $user['password'])) {
            unset($user['password']);
            $jwt = JWT::encode($user, Config::JWT_SECRET(), 'HS256');
            Flight::json(['token' => $jwt]);
        } else {
            Flight::json(["message" => "Wrong password"], 404);
        }
    } else {
        Flight::json(["message" => "User doesn't exist"], 404);
    }
});
/**
* @OA\Post(
*     path="/register",
*     description="Register to the system",
*     tags={"user"},
*     @OA\RequestBody(description="Basic user info", required=true,
*       @OA\MediaType(mediaType="application/json",
*    			@OA\Schema(
*    				@OA\Property(property="email", type="string", example="dino.keco@gmail.com",	description="Email"),
*    				@OA\Property(property="password", type="string", example="1234",	description="Password" ),
*    				@OA\Property(property="username", type="string", example="ajdin",	description="Username"),
*    				@OA\Property(property="firstname", type="string", example="Ajdin",	description="Firstname" ),
*    				@OA\Property(property="secondname", type="string", example="Hukić",	description="Secondname")
*        )
*     )),
*     @OA\Response(
*         response=200,
*         description="Success user registered"
*     ),
*     @OA\Response(
*         response=500,
*         description="Username or email is already registered"
*     )
* )
*/
Flight::route('POST /register', function () {
    $data = Flight::request()->data->getData();
    if (Flight::userDao()->get_user_by_username($data['username'])) {
        Flight::json(["message" => "Username already registered"], 500);
    } elseif (Flight::userDao()->get_user_by_email($data['email'])) {
        Flight::json(["message" => "Email already registered"], 500);
    } else {
        Flight::json(Flight::userDao()->addUser($data));
    }
});
/**
 * @OA\Get(path="/user", tags={"user"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return current logged in user id. ",
 *         @OA\Response( response=200, description="User id.")
 * )
 */
Flight::route('GET /user', function () {
    Flight::json(Flight::get('user')['id']);
});
