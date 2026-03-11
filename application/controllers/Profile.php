<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->load->model('User_model');
        $this->load->library('upload');
    }

    // GET /profile
    public function index()
    {
        $uid = $this->session->userdata('user_id');

        $currencies = $this->db
            ->select('code, name, symbol')
            ->from('currencies')
            ->order_by('code')
            ->get()
            ->result();

        $data['user']       = $this->currentUser();
        $data['profile']    = $this->User_model->getById($uid);
        $data['title']      = 'My Profile';
        $data['currencies'] = $currencies;

        $this->render('profile/index', $data);
    }

    // POST /profile/update
    public function update()
    {
        $uid = $this->session->userdata('user_id');

        $this->form_validation->set_rules('name',     'Name',     'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('currency', 'Currency', 'required|in_list[INR,USD,EUR,GBP,AED,JPY,CAD,AUD]');

        if ($this->form_validation->run() === FALSE) {
            $this->db->select('code, name, symbol')->from('currencies')->order_by('code');
            $data['user']       = $this->currentUser();
            $data['profile']    = $this->User_model->getById($uid);
            $data['title']      = 'My Profile';
            $data['currencies'] = $this->db->get()->result();
            $this->render('profile/index', $data);
            return;
        }

        // Check email uniqueness
        $email = $this->input->post('email', TRUE);
        if ($this->User_model->emailExistsForOther($email, $uid)) {
            $this->session->set_flashdata('error', 'That email is already in use.');
            redirect('profile');
            return;
        }

        $updateData = [
            'name'     => $this->input->post('name', TRUE),
            'email'    => $email,
            'currency' => $this->input->post('currency'),
        ];

        // Handle avatar upload
        if (! empty($_FILES['avatar']['name'])) {
            $uploadDir = FCPATH . 'assets/uploads/avatars/';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, TRUE);
            }

            $config = [
                'upload_path'   => $uploadDir,
                'allowed_types' => 'jpg|jpeg|png|gif|webp',
                'max_size'      => 2048,          // 2 MB
                'max_width'     => 1000,
                'max_height'    => 1000,
                'encrypt_name'  => TRUE,
            ];
            $this->upload->initialize($config);

            if ($this->upload->do_upload('avatar')) {
                $file = $this->upload->data();
                // Delete old avatar
                $current = $this->User_model->getById($uid);
                if ($current->avatar) {
                    $old = $uploadDir . $current->avatar;
                    if (file_exists($old)) {
                        @unlink($old);
                    }
                }
                $updateData['avatar'] = $file['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('profile');
                return;
            }
        }

        $this->User_model->updateProfile($uid, $updateData);

        // Refresh session name/email
        $this->session->set_userdata([
            'user_name'  => $updateData['name'],
            'user_email' => $updateData['email'],
        ]);

        $this->session->set_flashdata('success', 'Profile updated successfully.');
        redirect('profile');
    }

    // POST /profile/change-password
    public function change_password()
    {
        $uid = $this->session->userdata('user_id');

        $this->form_validation->set_rules('current_password', 'Current Password', 'required');
        $this->form_validation->set_rules('new_password',     'New Password',     'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run() === FALSE) {
            $this->db->select('code, name, symbol')->from('currencies')->order_by('code');
            $data['user']       = $this->currentUser();
            $data['profile']    = $this->User_model->getById($uid);
            $data['title']      = 'My Profile';
            $data['active_tab'] = 'password';
            $data['currencies'] = $this->db->get()->result();
            $this->render('profile/index', $data);
            return;
        }

        $user = $this->User_model->getById($uid);

        if (! password_verify($this->input->post('current_password'), $user->password)) {
            $this->session->set_flashdata('error', 'Current password is incorrect.');
            redirect('profile#password');
            return;
        }

        $newHash = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
        $this->User_model->updatePassword($uid, $newHash);

        $this->session->set_flashdata('success', 'Password changed successfully.');
        redirect('profile');
    }

    // POST /profile/remove-avatar
    public function remove_avatar()
    {
        $uid     = $this->session->userdata('user_id');
        $current = $this->User_model->getById($uid);

        if ($current->avatar) {
            $path = FCPATH . 'assets/uploads/avatars/' . $current->avatar;
            if (file_exists($path)) {
                @unlink($path);
            }
            $this->User_model->updateProfile($uid, ['avatar' => NULL]);
        }

        $this->session->set_flashdata('success', 'Avatar removed.');
        redirect('profile');
    }
}
