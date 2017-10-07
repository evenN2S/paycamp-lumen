<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    private $jwt;

    public function __construct(JWTAuth $jwt, UserRepository $user)
    {
        $this->jwt = $jwt;
        $this->user = $user;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name'    	=> 'required|max:255',
            'email'    	=> 'required|email|max:255',
            'password' 	=> 'required|min:6|max:32|confirmed',
        ]);

        if (! $user = $this->user->add($request->only('name', 'email', 'password')))
        	return response()->json([
        		'ack' => '05',
        		'msg' => 'INTERNAL SERVER ERROR'
        	], 500);

    	return response()->json([
    		'ack' => '00',
    		'msg' => 'REGISTER SUCCESS',
    		'data' => $user
    	], 200);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            if (! $token = $this->jwt->attempt($request->only('email', 'password')))
                return response()->json([
                	'ack' => '05',
                	'msg' => 'INVALID EMAIL OR PASSWORD'
                ], 404);
        } catch (TokenExpiredException $e) {
            return response()->json([
            	'ack' => '05',
            	'msg' => 'TOKEN EXPIRED'
           	], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json([
            	'ack' => '05',
            	'msg' => 'INVALID TOKEN'
            ], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json([
            	'ack' => '05',
            	'msg' => 'TOKEN NOT FOUND'
            ], $e->getStatusCode());
        }

        return response()->json([
        	'ack' => '00',
        	'msg' => 'LOGIN SUCCESS',
        	'token' => $token
        ], 200);
    }
}