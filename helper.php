<?php

use Illuminate\Support\Facades\Auth;

function admin()
{
    return Auth::guard('admin')->user();
}
function developer()
{
    return Auth::guard('developer')->user();
}