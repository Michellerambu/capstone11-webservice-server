<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Home extends BaseController
{
    protected $productModel;

    function __construct(){
        helper(['number', 'form']);
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $product = $this->productModel->findAll();
        $data['products'] = $product; 
        
        helper(['url', 'form']);
        
        return view('v_home', $data);
    }

    public function faq()
    {
        return view('v_faq');
    }

    public function profile()
    {
        $data = [
            'username'    => session()->get('username'),
            'role'        => session()->get('role'),
            'email'       => session()->get('email'),
            'waktu_login' => session()->get('waktu_login'),
            'status'      => session()->get('isLoggedIn') ? 'Online' : 'Offline',
        ];

        return view('v_profile', $data);
    }
}