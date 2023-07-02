<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    public function user()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll();

        return view('user/user', $data);
    }

    public function verifyUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user) {
            $user['is_aktif'] = true;
            $userModel->save($user);
            return redirect()->back()->with('success', 'User telah diverifikasi.');
        }

        return redirect()->back()->with('error', 'User tidak ditemukan.');
    }
}