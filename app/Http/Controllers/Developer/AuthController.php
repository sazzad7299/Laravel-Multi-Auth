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
      if(!Auth::guard('developer')->attempt($request->only('email','password'),$request->filled('remember'))){
          throw ValidationException::withMessages([
              'email'=> 'Invalid Email or Password',
              'password'=> 'Invalid Email or Password'
          ]);
      }
      return redirect()->intended(route('developer.home'));
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

    Auth::login($user = Developer::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]));
  }
  public function destroy(Request $request)
    {
        Auth::guard('developer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
