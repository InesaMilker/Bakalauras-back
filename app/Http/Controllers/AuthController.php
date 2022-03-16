<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials))
        {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth('api')->factory()
            ->getTTL() * 60, ]);
    }

    public function logout()
    {

        auth()
            ->logout();
        return response()
            ->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()
            ->refresh());
    }

    public function me()
    {
        return response()
            ->json(auth()
            ->user());
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all() , ['name' => 'required|string|between:2,100', 'email' => 'required|string|email|max:100|unique:users', 'password' => 'required|string|confirmed|min:6', ]);

        if ($validator->fails())
        {
            return response()
                ->json($validator->errors()
                ->toJson() , 400);
        }

        $user = User::create(array_merge($validator->validated() , ['password' => bcrypt($request->password) ]));

        return response()
            ->json(['message' => 'User successfully registered', 'user' => $user], 201);
    }
}