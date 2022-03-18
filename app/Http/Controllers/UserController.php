<?php
namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Illuminate\Support\Facades\DB;
    use App\Traits\Verifytoken;
    use Illuminate\Database\QueryException;

class UserController extends Controller
{
    use Verifytoken;
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)){
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $user=User::where('email','=',$request->email)->first();
        return response()->json(['data' => $user, 'code' => 200,'token'=>$token]);
     //   return response()->json(compact('token'));
    }
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    public function index()
    {
    return User::where('id', '!=', 1)->get();
    }

    public function create(Request $request)
    {
        try {
            $userexist = User::where('email', '=', $request->email)->get();
            if(count($userexist)!==0) {
                return response()->json(['data'=>[],"message"=>"Este correo ya ha sido registrado", 'code'=>400],400);
            }else{

                $validated = $request->validate([
                    'email' => 'required|email|unique:users,email,'.$request->email,
                    'nombre'=>'required',
                    'apellido'=>'required',
                    'tel_mov'=>'required',
                    'password' =>'required',
                    'token'=>'required'
                ]);
                // return $validated;
                if($this->verifica($request->token)){
                // return "si es valido";
                    // $claveinicial="CanastasYArcones";
                    $user= new User();
                    $user->nom=$request->nombre;
                    $user->email=$request->email;
                    $user->apell=$request->apellido;
                    $user->acceso=2;
                    $user->email_registro=$request->email;
                    $user->tel_mov=$request->tel_mov;
                    // $user->password = bcrypt($claveinicial);
                    $user->password = bcrypt($request->password);
                    // $user->password = bcrypt($pas);
                    $user->asignado_us="API";
                    $user->created_at= date('Y-m-d H:i:s');
                    $user->created_at_us= $request->email;
                    // return $user;
                    $user->save();
                    DB::table('model_has_roles')->insert([
                        'role_id' => '2',
                        'model_type' => 'App\User',
                        'model_id'=> $user->id
                    ]);
                    return response()->json(['data'=>[],"message"=>"Usuario regristrado con éxito","code"=>201]);
                }else{
                    return response()->json(['data'=>[],"message"=>"Token invalido","code"=>403]);
                }
            }
        } catch (\Throwable $th) {
            // return $ex->getMessage();
            return response(["message"=>"error", 'error'=>$th->getMessage(),'code'=>404]);
        }
    }
}


