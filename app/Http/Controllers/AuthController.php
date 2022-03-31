<?php
namespace App\Http\Controllers;

use App\Models\Coordinates;
use App\Models\Day;
use App\Models\Trips;
use App\Models\Diary;
use App\Models\Outfit;
use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
            return response()
            ->json(['error' => 'Invalid email or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()
        ->json(['access_token' => $token, 'token_type' => 'bearer', 
        'expires_in' => auth('api')->factory()
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
        return $this->respondWithToken(auth()->refresh());
    }

    public function me()
    {
        return response()
            ->json(auth()
            ->user());
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all() , 
        ['name' => 'required|string|between:2,100', 
        'email' => 'required|string|email|max:100|unique:users', 
        'password' => 'required|string|confirmed|min:6', ]);

        if ($validator->fails())
        {
            return response()
                ->json($validator->errors()
                ->toJson() , 400);
        }

        $user = User::create(array_merge($validator->validated() ,
         ['password' => bcrypt($request->password) ]));

        return response()
            ->json(['message' => 'User successfully registered', 'user' => $user], 201);
    }

    public function destroy()
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        Coordinates::where('user_id', $user_id)->delete();
        Day::where('user_id', $user_id)->delete();
        Diary::where('user_id', $user_id)->delete();
        Trips::where('user_id', $user_id)->delete();
        Checklist::where('user_id', $user_id)->delete();
        Outfit::where('user_id', $user_id)->delete();

        $user->delete();
        
        if($user->delete()){
            return response()->json(['success'], 200);
        }
    }
}