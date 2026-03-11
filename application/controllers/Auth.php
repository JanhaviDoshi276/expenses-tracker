<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login()
    {
        $this->requireGuest();
        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    public function login_post()
    {
        $this->requireGuest();

        $this->form_validation->set_rules('email',    'Email',    'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Login';
            $this->load->view('auth/login', $data);
            return;
        }

        $email    = $this->input->post('email', TRUE);
        $password = $this->input->post('password');
        $user     = $this->User_model->getByEmail($email);

        if ($user && $user->status == 1 && password_verify($password, $user->password)) {
            $this->session->set_userdata([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'user_role'  => $user->role,
            ]);
            redirect('dashboard');
        }

        $data['title'] = 'Login';
        $data['error']  = ($user && $user->status == 0)
            ? 'Your account has been deactivated. Please contact admin.'
            : 'Invalid email or password.';
        $this->load->view('auth/login', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}
