<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->load->model(['Category_model','User_model']);
    }

    public function index()
    {
        $search = $this->input->get('search', TRUE);
        $data['user']       = $this->currentUser();
        $data['title']      = 'Manage Categories';
        $data['categories'] = $this->Category_model->getAll($search);
        $data['search']     = $search;
        $this->render('categories/index', $data);
    }

    // POST /categories/store (AJAX)
    public function store()
    {
        $this->requireAjax();
        $this->form_validation->set_rules('name',  'Name',  'required|trim|max_length[80]');
        $this->form_validation->set_rules('icon',  'Icon',  'required|trim');
        $this->form_validation->set_rules('color', 'Color', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $id = $this->Category_model->create([
            'name'  => $this->input->post('name', TRUE),
            'icon'  => $this->input->post('icon', TRUE),
            'color' => $this->input->post('color', TRUE),
        ]);

        $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Category created successfully.']);
    }

    // GET /categories/get/:id (AJAX)
    public function get($id)
    {
        $this->requireAjax();
        $cat = $this->Category_model->getById($id);
        if ( ! $cat) $this->jsonResponse(['success' => false], 404);
        $this->jsonResponse(['success' => true, 'category' => $cat]);
    }

    // POST /categories/update/:id (AJAX)
    public function update($id)
    {
        $this->requireAjax();
        $cat = $this->Category_model->getById($id);
        if ( ! $cat) $this->jsonResponse(['success' => false, 'message' => 'Not found.'], 404);

        $this->form_validation->set_rules('name',  'Name',  'required|trim|max_length[80]');
        $this->form_validation->set_rules('icon',  'Icon',  'required|trim');
        $this->form_validation->set_rules('color', 'Color', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $updateData = [
            'name'  => $this->input->post('name', TRUE),
            'icon'  => $this->input->post('icon', TRUE),
            'color' => $this->input->post('color', TRUE),
        ];

        $this->Category_model->update($id, $updateData);
        $this->jsonResponse(['success' => true, 'message' => 'Category updated. All related expenses reflect this change automatically.']);
    }

    // POST /categories/delete/:id (AJAX)
    public function delete($id)
    {
        $this->requireAjax();
        $cat = $this->Category_model->getById($id);
        if ( ! $cat) $this->jsonResponse(['success' => false, 'message' => 'Not found.'], 404);
        if ($cat->is_default) {
            $this->jsonResponse(['success' => false, 'message' => 'Default category cannot be deleted.']);
        }

        $this->Category_model->delete($id);
        $this->jsonResponse(['success' => true, 'message' => 'Category deleted. Related expenses moved to Unspecified.']);
    }
}
