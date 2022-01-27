<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // $request->authenticate();

        // $request->session()->regenerate();

        // return redirect(RouteServiceProvider::HOME);
        if($request->isMethod('post')){
            $data = $request->all();
            // dd($data);
            $userStatus= User::where(['email'=>$data['email']])->count();

            // dd($userStatus->password);
            if($userStatus == 0){
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }else{
                $userStatus= User::where(['email'=>$data['email']])->first();
                if(Hash::check($data['password'], $userStatus->password)){
                    if(empty($userStatus->email_verified_at)){
                      return redirect()->back()->with('inactive','Please activate your Account before login');
                    }else{
                      Auth::guard('web')->attempt(['email' => $data['email'], 'password' => $data['password']]);
                      return redirect()->intended(route('home'));
                    }
                    } else{
                      throw ValidationException::withMessages([
                        'email' => __('auth.failed'),
                    ]);
                    }
            }
          }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($code)
    {
        $email =base64_decode($code);
        // dd($email);
        $userCount = User::where('email',$email)->count();
        if($userCount >0){
            $userDetails = User::where('email',$email)->first();
            // dd($userDetails->status);
            if($userDetails->status==1){
                return redirect()->intended(route('login'))->with('warning','Your Email Account already verified. You can Login');
            }else{
                User::where('email',$email)->update(['email_verified_at'=>Carbon::now(),'status'=>1]);
                return redirect()->intended(route('login'))->with('success','Your Email Account has been verified successfully. You can Login');
            }
        }else{
            abort(404);
        }
    }
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
