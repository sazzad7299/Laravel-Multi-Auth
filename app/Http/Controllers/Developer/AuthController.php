<?php

namespace App\Http\Controllers\Developer;

use Carbon\Carbon;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function store(Request $request)
  {

    if($request->isMethod('post')){
      $data = $request->all();
      $userStatus= Developer::where('email',$data['email'])->first();
      if(Hash::check($data['password'], $userStatus->password)){
      if($userStatus->status==0){
        return redirect()->back()->with('flash_login_massage_error','Please activate your Account before login');
      }else{
        Auth::guard('developer')->attempt(['email' => $data['email'], 'password' => $data['password']]);
        return redirect()->intended(route('developer.home'));
      }
      } else{
        throw ValidationException::withMessages([
          'email' => __('auth.failed'),
      ]);
      }
    }
    
  }
  public function register(Request $request)
  {
    //   $data = $request->all();
    //   dd($data);
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:developers',
        'password' => 'required|string|confirmed|min:8',
    ]);
      $user = Developer::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);
      $data= $request->all();
      $email =$data['email'];
      $messageData = ['email'=>$data['email'],'name'=>$data['name'],'code'=>base64_encode($email)];
      Mail::send('auth.email.verify',$messageData,function($message) use($email){
          $message->to($email)->subject('Verify Your Email!');
      });
    return redirect()->back()->with('success','Your Account Created Successfully. Please Verify your mail before login');

  }
  public function verified($code)
    {
      $email =base64_decode($code);
      $userCount = Developer::where('email',$email)->count();
      if($userCount > 0){
        $userDetails = Developer::where('email',$email)->first();
        if($userDetails->status==1){
          return redirect()->route('developer.login')->with('warning','Your Email Account already verified. You can Login');
        }
        else{
          Developer::where('email',$email)->update(['email_verified_at'=>Carbon::now(),'status'=>1]);
          return redirect()->route('developer.login')->with('success','Your Email Account has been verified successfully. You can Login');
      }
      }
    }
    public function forget()
    {
      return view('auth.passwords.forget');
    }
    public function generatePassword(Request $request)
    {
      $developerCount = Developer::where(['email'=>$request->email])->count();
      if($developerCount>0){
        $developer = Developer::where(['email'=>$request->email])->first();
        $random_pss= Str::random(8);
        $new_password = Hash::make($random_pss);
        Developer::where(['email'=>$request->email])->update(['password'=>$new_password]);
        $name = $developer->name;
        // echo "<pre>";print_r($userDetail->name);die;

        // Send Random password to user by Mail
        $email = $request->email;
        $messageData=[
            'name'=>$name,
            'email'=>$email,
            'password'=>$random_pss
        ];
        Mail::send('auth.email.newpassword',$messageData,function($message) use($email){
            $message->to($email)->subject('Reset Password --EASY_SHOP');
        });
        return redirect()->route('developer.login')->with('success','Password Reset Successfully. Please check email Mail');

      }else{
        throw ValidationException::withMessages([
          'email' => __('auth.failed'),
        ]);
      }
    }
  public function destroy(Request $request)
    {
        Auth::guard('developer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('developer/login');
    }
}
