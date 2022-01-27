<?php

namespace App\Http\Controllers\Developer;

use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|confirmed|min:8',
    ]);
    
    $user = Developer::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    return redirect()->back()->with('success','Your Account Created Successfully. Please Verify your mail before login');
  }
  public function destroy(Request $request)
    {
        Auth::guard('developer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
