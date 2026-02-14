<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirect('/admin');
        }
        return $this->view('auth.login', ['title' => 'Login | Titan Guard']);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::attempt($email, $password)) {
            return $this->redirect('/admin');
        }

        return $this->view('auth.login', [
            'error' => 'Invalid credentials.',
            'title' => 'Login | Titan Guard'
        ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirect('/admin');
        }
        return $this->view('auth.register', ['title' => 'Register | Titan Guard']);
    }

    public function register(Request $request)
    {
        $data = $request->all();

        // Basic validation (ideally use Validator)
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return $this->view('auth.register', [
                'error' => 'All fields are required.',
                'title' => 'Register | Titan Guard'
            ]);
        }

        if (User::where('email', $data['email'])->first()) {
            return $this->view('auth.register', [
                'error' => 'Email already registered.',
                'title' => 'Register | Titan Guard'
            ]);
        }

        $user = User::register($data);

        if ($user) {
            Auth::login($user);
            return $this->redirect('/admin');
        }

        return $this->view('auth.register', [
            'error' => 'Registration failed.',
            'title' => 'Register | Titan Guard'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return $this->redirect('/');
    }
}
