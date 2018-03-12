<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    //
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request,[
            'email' => 'required|email|max:255|exists:users',
            'password' => 'required|min:6|max:16'
        ]);

        if ( Auth::attempt($data) ) {
            session()->flash('success','欢迎回来！');
            dd(session('success'),Auth::user());
        }else{
            session()->flash('error','邮箱或密码错误');
            dd(session('error'),Auth::user());
        }


    }

    public function destroy( User $user )
    {

    }

}
