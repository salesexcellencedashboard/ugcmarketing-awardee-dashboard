<?php

namespace App\Controllers;

use App\Models\UserModel;

class SettingsController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $userId = session()->get('user_id');
        
        // Get current user data
        $user = null;
        if ($userId) {
            $user = $userModel->find($userId);
        }
        
        // Get all admin users (excluding current user for management list)
        $admins = $userModel->where('role', 'admin')->findAll();
        
        return view('settings/index', [
            'pageTitle' => 'Settings',
            'user'      => $user,
            'admins'    => $admins,
        ]);
    }

    public function updateProfile()
    {
        helper(['form', 'url']);
        
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/settings');
        }

        $rules = [
            'fullname' => 'required|min_length[3]|max_length[150]',
            'email'    => 'required|valid_email|max_length[120]',
        ];

        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/settings')->with('profile_error', 'User not authenticated.');
        }

        // If email changed, check uniqueness
        $userModel = new UserModel();
        $currentUser = $userModel->find($userId);
        if (!$currentUser) {
            return redirect()->to('/settings')->with('profile_error', 'User not found.');
        }

        $newEmail = trim((string) $this->request->getPost('email'));
        if ($newEmail !== $currentUser['email']) {
            $rules['email'] = 'required|valid_email|max_length[120]|is_unique[users.email]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('profile_errors', $this->validator->getErrors());
        }

        $fullname = trim((string) $this->request->getPost('fullname'));
        $username = trim((string) $this->request->getPost('username'));

        // Check username uniqueness if changed
        if ($username !== $currentUser['username']) {
            $existing = $userModel->where('username', $username)->where('id !=', $userId)->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('profile_error', 'Username already taken.');
            }
        }

        $updateData = [
            'fullname'   => $fullname,
            'username'   => $username,
            'email'      => $newEmail,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Update password if provided
        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');
        
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return redirect()->back()->withInput()->with('profile_error', 'Password must be at least 6 characters.');
            }
            if ($password !== $confirmPassword) {
                return redirect()->back()->withInput()->with('profile_error', 'Passwords do not match.');
            }
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $updateData);

        // Update session data
        session()->set('fullname', $fullname);
        session()->set('username', $username);
        session()->set('email', $newEmail);

        return redirect()->to('/settings')->with('profile_success', 'Profile updated successfully.');
    }

    public function registerAdmin()
    {
        helper(['form', 'url']);
        
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/settings');
        }

        $rules = [
            'fullname'      => 'required|min_length[3]|max_length[150]',
            'username'      => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'         => 'required|valid_email|max_length[120]|is_unique[users.email]',
            'password'      => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('admin_errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $data = [
            'fullname'      => trim((string) $this->request->getPost('fullname')),
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'admin',
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $userModel->insert($data);

        return redirect()->to('/settings')->with('admin_success', 'New admin registered successfully.');
    }

    public function deactivateAdmin($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/settings')->with('admin_error', 'User not found.');
        }

        // Prevent deactivating yourself
        if ((int)$id === (int)session()->get('user_id')) {
            return redirect()->to('/settings')->with('admin_error', 'You cannot deactivate your own account.');
        }

        $userModel->update($id, [
            'status'     => 'inactive',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/settings')->with('admin_success', 'Admin deactivated successfully.');
    }

    public function uploadProfilePic()
    {
        helper(['form', 'url']);
        
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/settings');
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/settings')->with('profile_error', 'User not authenticated.');
        }

        $file = $this->request->getFile('profile_pic');
        
        if (!$file || !$file->isValid()) {
            return redirect()->to('/settings')->with('profile_error', 'Please select a valid image file.');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return redirect()->to('/settings')->with('profile_error', 'Only JPG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->to('/settings')->with('profile_error', 'Image size must be less than 2MB.');
        }

        // Delete old profile pic if exists
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if ($user && $user['profile_pic']) {
            $oldFile = FCPATH . $user['profile_pic'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Generate unique filename
        $newName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();
        
        // Ensure directory exists
        $uploadPath = FCPATH . 'uploads/profiles';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $newName);

        // Save path in database
        $profilePath = '/uploads/profiles/' . $newName;
        $userModel->update($userId, ['profile_pic' => $profilePath]);

        return redirect()->to('/settings')->with('profile_success', 'Profile picture updated successfully.');
    }

    public function activateAdmin($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/settings')->with('admin_error', 'User not found.');
        }

        $userModel->update($id, [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/settings')->with('admin_success', 'Admin activated successfully.');
    }
}