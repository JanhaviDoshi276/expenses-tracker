<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->load->model(['User_model', 'Category_model']);
    }

    public function index()
    {
        $search = $this->input->get('search', TRUE);

        // Fetch currencies first
        $currencies = $this->db
            ->select('code, name, symbol')
            ->from('currencies')
            ->order_by('code')
            ->get()
            ->result();

        $data['user']       = $this->currentUser();
        $data['title']      = 'User Management';
        $data['users']      = $this->User_model->getAll($search);
        $data['search']     = $search;
        $data['categories'] = $this->Category_model->getAll();
        $data['access_map'] = $this->User_model->getAllCategoryAccessMap();
        $data['currencies'] = $currencies;

        $this->render('users/index', $data);
    }

    // GET /users/get/:id (AJAX)
    public function get($id)
    {
        $this->requireAjax();
        $user = $this->User_model->getById($id);
        if (! $user) $this->jsonResponse(['success' => false], 404);

        $access = $this->User_model->getCategoryAccess($id);
        $user->category_ids = array_column($access, 'category_id');
        $this->jsonResponse(['success' => true, 'user' => $user]);
    }

    // POST /users/store (AJAX)
    public function store()
    {
        $this->requireAjax();

        $this->form_validation->set_rules('name',     'Name',     'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email',    'Email',    'required|valid_email|trim|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role',     'Role',     'required|in_list[admin,user]');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $userId = $this->User_model->create([
            'name'     => $this->input->post('name', TRUE),
            'email'    => $this->input->post('email', TRUE),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role'     => $this->input->post('role'),
            'status'   => 1,
            'currency' => $this->input->post('currency') ?: 'INR',
        ]);

        // Set category access
        $categoryIds = $this->input->post('category_ids') ?: [];
        if ($this->input->post('role') === 'admin') {
            // Admins get all categories
            $allCats = $this->Category_model->getAll();
            $categoryIds = array_column((array)$allCats, 'id');
        }
        $this->User_model->setCategoryAccess($userId, $categoryIds);

        $this->jsonResponse(['success' => true, 'id' => $userId, 'message' => 'User created successfully.']);
    }

    // POST /users/update/:id (AJAX)
    public function update($id)
    {
        $this->requireAjax();

        $user = $this->User_model->getById($id);
        if (! $user) $this->jsonResponse(['success' => false, 'message' => 'Not found.'], 404);

        // Cannot change superadmin role
        if ($user->role === 'superadmin') {
            $this->jsonResponse(['success' => false, 'message' => 'Superadmin cannot be modified.']);
        }

        $this->form_validation->set_rules('name',  'Name',  'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('role',  'Role',  'required|in_list[admin,user]');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $newEmail = $this->input->post('email', TRUE);
        if ($this->User_model->emailExistsForOther($newEmail, $id)) {
            $this->jsonResponse(['success' => false, 'errors' => ['email' => 'Email already in use.']]);
        }

        $updateData = [
            'name'     => $this->input->post('name', TRUE),
            'email'    => $newEmail,
            'role'     => $this->input->post('role'),
            'status'   => (int)$this->input->post('status'),
            'currency' => $this->input->post('currency') ?: 'INR',
        ];

        $newPassword = $this->input->post('password');
        if ($newPassword) {
            if (strlen($newPassword) < 6) {
                $this->jsonResponse(['success' => false, 'errors' => ['password' => 'Min 6 characters.']]);
            }
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->User_model->update($id, $updateData);

        // Update category access
        $categoryIds = $this->input->post('category_ids') ?: [];
        if ($this->input->post('role') === 'admin') {
            $allCats = $this->Category_model->getAll();
            $categoryIds = array_column((array)$allCats, 'id');
        }
        $this->User_model->setCategoryAccess($id, $categoryIds);

        $this->jsonResponse(['success' => true, 'message' => 'User updated successfully.']);
    }

    // POST /users/delete/:id (AJAX)
    public function delete($id)
    {
        $this->requireAjax();
        $user = $this->User_model->getById($id);
        if (! $user) $this->jsonResponse(['success' => false, 'message' => 'Not found.'], 404);
        if ($user->role === 'superadmin') {
            $this->jsonResponse(['success' => false, 'message' => 'Superadmin cannot be deleted.']);
        }
        // Prevent deleting self
        if ($id == $this->session->userdata('user_id')) {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot delete your own account.']);
        }

        $this->User_model->delete($id);
        $this->jsonResponse(['success' => true, 'message' => 'User deleted successfully.']);
    }

    // POST /users/toggle-status/:id (AJAX)
    public function toggle_status($id)
    {
        $this->requireAjax();
        $user = $this->User_model->getById($id);
        if (! $user || $user->role === 'superadmin') {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot modify this user.']);
        }
        $newStatus = $user->status ? 0 : 1;
        $this->User_model->update($id, ['status' => $newStatus]);
        $this->jsonResponse(['success' => true, 'status' => $newStatus, 'message' => $newStatus ? 'User activated.' : 'User deactivated.']);
    }
}
