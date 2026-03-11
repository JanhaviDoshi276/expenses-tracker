<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $data = [];

    public function __construct()
    {
        parent::__construct();
    }

    protected function render($view, $data = [])
    {
        $data['content_view'] = $view;
        $this->load->view('layouts/main', $data);
    }

    protected function requireAuth()
    {
        if ( ! $this->session->userdata('user_id')) {
            redirect('login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        $role = $this->session->userdata('user_role');
        if ( ! in_array($role, ['superadmin', 'admin'])) {
            show_error('Access denied. Admin privileges required.', 403);
        }
    }

    protected function requireSuperAdmin()
    {
        $this->requireAuth();
        if ($this->session->userdata('user_role') !== 'superadmin') {
            show_error('Access denied. Superadmin privileges required.', 403);
        }
    }

    protected function requireGuest()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }
    }

    protected function currentUser()
    {
        return [
            'id'    => $this->session->userdata('user_id'),
            'name'  => $this->session->userdata('user_name'),
            'email' => $this->session->userdata('user_email'),
            'role'  => $this->session->userdata('user_role'),
        ];
    }

    protected function isSuperAdmin()
    {
        return $this->session->userdata('user_role') === 'superadmin';
    }

    protected function isAdmin()
    {
        return in_array($this->session->userdata('user_role'), ['superadmin', 'admin']);
    }

    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAjax()
    {
        if ( ! $this->input->is_ajax_request()) {
            show_error('Direct access not allowed.', 400);
        }
    }
}
