<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\admin\Users_logs_model;
use App\Models\User;
use App\Models\User_old;
use App\Providers\RouteServiceProvider;
use Auth;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME; //ANTES HOME

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'change_user']);
    }
    public function username()
    {
        return 'username';
    }

    public function change_user(Request $request)
    {
        $usr = User::select('username', 'password')->where('id', '=', $request->iduser)->first();
        if (!is_null($usr)) {
            \Auth::guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if (\Auth::attempt(['username' => $usr->username, 'password' => 'sigema'])) {
                // notify()->success('Cambio de usuario correcto!');
                return response()->json(['success' => 'true', 'Msg' => 'Buscando...']);
            } else {
                return response()->json(['success' => 'false', 'Msg' => 'Wait...']);
            }
        } else {
            return response()->json(['success' => 'false', 'Msg' => 'Error de datos']);
        }
    }

    // public function login_singogle(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);
    //     $usr = User::select('userpassword')->where('username', '=', $request->username)->first();
    //     if (!is_null($usr)) {
    //         if (\Hash::check($request->password, $usr->userpassword)) {
    //             if (\Auth::attempt(['username' => $request->username, 'password' => 'sigema'])) {
    //                 User::where('id', Auth::user()->id)->update(['last_login' => date("Y-m-d H:i:s")]); //actualza ingreso
    //                 $log = array('idUsuario' => Auth::user()->id, 'usuario' => Auth::user()->username, 'nombre' => Auth::user()->name, 'accion' => 'Ingreso', 'tipoAccion' => 1, 'logia' => Auth::user()->idLogia, 'valle' => Auth::user()->idValle, 'rol' => Auth::user()->idRol);
    //                 Users_logs_model::insert($log); //inserta log
    //                 return \Redirect::to('inicio');
    //             }
    //         }
    //     }
    //     return redirect("login")->withErrors(["username" => "Datos de usuario erroneos!"]);
    // }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Debe completar el catpcha',
        ]);
        $secret = $_ENV['CAPTCHA_SECRET'];
        $response = $_POST["g-recaptcha-response"];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
        $captcha_success = json_decode($verify);

        if ($captcha_success->success == false) {
            return redirect("login")->withErrors(["g-recaptcha-response" => "Robot detectado!"]);
            // echo "Robot detectado";
            // exit;
        }
        $usr = User::select('userpassword')->where('username', '=', $request->username)->first();
        if (!is_null($usr)) {
            if (\Hash::check($request->password, $usr->userpassword)) {
                if (\Auth::attempt(['username' => $request->username, 'password' => 'sigema'])) {
                    User::where('id', Auth::user()->id)->update(['last_login' => date("Y-m-d H:i:s")]); //actualza ingreso
                    $log = array('idUsuario' => Auth::user()->id, 'usuario' => Auth::user()->username, 'nombre' => Auth::user()->name, 'accion' => 'Ingreso', 'tipoAccion' => 1, 'logia' => Auth::user()->idLogia, 'valle' => Auth::user()->idValle, 'rol' => Auth::user()->idRol);
                    Users_logs_model::insert($log); //inserta log
                    return \Redirect::to('inicio');
                }
            }
        }
        return redirect("login")->withErrors(["username" => "Datos de usuario erroneos!"]);
    }
    private function login_old($user, $passw)
    {
        $usr = User_old::select('username', 'password')->where('username', '=', $user)->first();
    }
}
