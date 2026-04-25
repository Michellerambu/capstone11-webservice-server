<?php

namespace App\Controllers;

class ProfileController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $data = [
            'username'    => session()->get('username'),
            'role'        => session()->get('role'),
            'email'       => session()->get('email'),
            'waktu_login' => session()->get('waktu_login'),
            'status'      => session()->get('isLoggedIn') ? 'Sudah Login' : 'Belum Login'
        ];

        return view('v_profile', $data);
    }
}