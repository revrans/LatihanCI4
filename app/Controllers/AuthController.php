<?php

namespace App\Controllers;

use App\Models\userModel;

class AuthController extends BaseController
{
    protected $user;

    function __construct()
    {
        helper('form');
        $this->user = new userModel();
    }

    public function login()
    {
        if ($this->request->getPost()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $dataUser = $this->user->where(['username' => $username])->first();

            if ($dataUser) {
                if ($dataUser['is_aktif'] == 1) {
                    if (md5($password) == $dataUser['password']) {
                        session()->set([
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE,
                        ]);

                        return redirect()->to(base_url('/'));
                    } else {
                        session()->setFlashdata('failed', 'Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Anda saat ini masih belum aktif, lakukan aktivasi terlebih dahulu');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                return redirect()->back();
            }
        } else {
            return view('Pages/login');
        }
    }
    public function register()
    {
        if ($this->request->getPost()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $name = $this->request->getVar('your_name');
            $email = $this->request->getVar('your_email');

            $dataUser = $this->user->where(['username' => $username])->first();

            if ($dataUser) {
                session()->setFlashdata('failed', 'Username already exists');
                return redirect()->back();
            }

            // Simpan data user ke dalam database
            $userData = [
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => md5($password), // Untuk sementara menggunakan MD5, sebaiknya menggunakan metode enkripsi yang lebih aman
                'role' => 'user',
                'is_aktif' => true,
            ];

            $this->user->insert($userData);

            session()->setFlashdata('success', 'Registration successful. Please login.');
            return redirect()->to('login');
        } else {
            return view('Pages/register');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
