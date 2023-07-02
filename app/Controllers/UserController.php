<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Validation\Validation;

class UserController extends BaseController
{
    protected $user;
    protected $validation;
    protected $userModel;

    function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->user = new UserModel();
        $this->userModel = new UserModel();
    }

    public function user()
    {
        $data['users'] = $this->user->findAll();
        return view('user/user', $data);
    }

    // public function create()
    // {
    //     return view('user/create');
    // }
    public function create()
    {
        $data = $this->request->getPost();
        $validate = $this->validation->run($data, 'user');
        $errors = $this->validation->getErrors();
        
        if (!$errors) {
            $dataForm = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'username' => $this->request->getPost('username'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'is_aktif' => $this->request->getPost('is_aktif') ? 1 : 0
            ];

            // Process and store new user data to the database
            $this->user->insert($dataForm);

            return redirect()->to('/user')->with('success', 'User created successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $errors);
        }
    }


    public function store()
    {
        $data = [
            'name' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => md5($this->request->getPost('password')),
            'role' => $this->request->getPost('role'),
            'is_aktif' => $this->request->getPost('is_aktif'),
        ];
        // $data = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
            'is_aktif' => 'in_list[0,1]',
        ]);

        if ($validation->run($data)) {
            // Valid data, proceed to store in the database
            $this->userModel->insert($data);

            return redirect()->to('user')->with('success', 'User added successfully.');
        } else {
            // Invalid data, show error message
            $errorMessages = implode('<br>', $validation->getErrors());
            return redirect()->to('user')->with('failed', $errorMessages);
        }
    }



    public function edit($id)
    {
        $data['user'] = $this->user->find($id);

        if (!$data['user']) {
            return redirect()->to('/user')->with('error', 'User not found.');
        }

        return view('user/edit', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $validate = $this->validation->run($data, 'user');
        $errors = $this->validation->getErrors();

        if (!$errors) {
            // Proses pembaruan data pengguna ke database
            // ...

            return redirect()->to('/user')->with('success', 'User updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $errors);
        }
    }

    public function delete($id)
    {
        // Lakukan validasi ID pengguna
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id' => 'required|integer'
        ]);
    
        if (!$validation->run(['id' => $id])) {
            return redirect()->to('/user')->with('failed', 'ID pengguna tidak valid');
        }
    
        // Cari pengguna dengan ID yang diberikan
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);
    
        if ($user) {
            // Hapus pengguna dengan ID yang diberikan
            $userModel->delete($id);
    
            // Redirect kembali ke halaman pengguna dengan pesan sukses
            return redirect()->to('/user')->with('success', 'Pengguna berhasil dihapus');
        } else {
            // Redirect kembali ke halaman pengguna dengan pesan gagal
            return redirect()->to('/user')->with('failed', 'Pengguna tidak ditemukan');
        }
    }
    

    public function activate($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return redirect()->to('/user')->with('error', 'User not found.');
        }

        // Update the `is_aktif` field of the user to activate
        $this->user->update($id, ['is_aktif' => 1]);

        return redirect()->to('/user')->with('success', 'User activated successfully.');
    }

    public function deactivate($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return redirect()->to('/user')->with('error', 'User not found.');
        }

        // Update the `is_aktif` field of the user to deactivate
        $this->user->update($id, ['is_aktif' => 0]);

        return redirect()->to('/user')->with('success', 'User deactivated successfully.');
    }
}