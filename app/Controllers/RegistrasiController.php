<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;

class RegistrasiController extends BaseController
{
    protected $user;

    function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->user = new userModel();
    }

    public function index()
    {
        return view('Pages/registrasi_view');
    }

    public function register()
    {
        $username = $this->request->getVar('username');
        $enkrip = $this->request->getVar('password');
        $errors = $this->validation->getErrors();
        $md5 = md5($enkrip);

        if (!$errors) {
            $dataForm = [
                'username' => $this->request->getPost('username'),
                'role' => 'user',
                'password' => $md5,
                'is_aktif' => true
            ];
            $this->user->insert($dataForm);

            return redirect()->to('login');
        } else {
            return redirect('login')->with('failed', implode("<br>", $errors));
        }
    }
}