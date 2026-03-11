<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->load->model(['Expense_model', 'Category_model', 'User_model', 'ExchangeRate_model']);
    }

    // GET /expenses — transaction history
    public function index()
    {
        $uid   = $this->session->userdata('user_id');
        $role  = $this->session->userdata('user_role');
        $accessIds = $this->User_model->getAccessibleCategoryIds($uid, $role);

        $filters = [
            'category_id' => $this->input->get('category_id'),
            'month'       => $this->input->get('month'),
            'year'        => $this->input->get('year') ?: date('Y'),
            'search'      => $this->input->get('search', TRUE),
        ];

        $data['user']       = $this->currentUser();
        $data['title']      = 'Transaction History';
        $data['expenses']   = $this->Expense_model->getFiltered($uid, $filters, $accessIds);
        $data['categories'] = $this->Category_model->getAccessible($uid, $role);
        $data['filters']    = $filters;
        $data['total']      = $this->Expense_model->filteredTotal($uid, $filters, $accessIds);

        $this->db->select('code, name, symbol')->from('currencies')->order_by('code');
        $data['currencies'] = $this->db->get()->result();

        $this->render('expenses/index', $data);
    }

    // POST /expenses/store (AJAX)
    public function store()
    {
        $this->requireAjax();
        $uid  = $this->session->userdata('user_id');
        $role = $this->session->userdata('user_role');

        $this->form_validation->set_rules('title',        'Title',        'required|trim|max_length[200]');
        $this->form_validation->set_rules('amount',       'Amount',       'required|decimal|greater_than[0]');
        $this->form_validation->set_rules('expense_date', 'Date',         'required');
        $this->form_validation->set_rules('category_id',  'Category',     'required|integer');
        $this->form_validation->set_rules('currency',     'Currency',     'required');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $categoryId = (int)$this->input->post('category_id');
        // Validate category access
        $accessIds = $this->User_model->getAccessibleCategoryIds($uid, $role);
        if ($accessIds !== null && ! in_array($categoryId, array_map('intval', $accessIds))) {
            $this->jsonResponse(['success' => false, 'message' => 'Access denied to this category.'], 403);
        }

        $id = $this->Expense_model->create([
            'user_id'      => $uid,
            'category_id'  => $categoryId,
            'title'        => $this->input->post('title', TRUE),
            'amount'       => $this->input->post('amount'),
            'currency'     => $this->input->post('currency'),
            'expense_date' => $this->input->post('expense_date'),
            'note'         => $this->input->post('note', TRUE),
            'added_by'     => $uid,
        ]);

        $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Expense added successfully.']);
    }

    // POST /expenses/update/:id (AJAX)
    public function update($id)
    {
        $this->requireAjax();
        $uid  = $this->session->userdata('user_id');
        $role = $this->session->userdata('user_role');

        $expense = $this->Expense_model->getOne($id);
        if ( ! $expense) {
            $this->jsonResponse(['success' => false, 'message' => 'Expense not found.'], 404);
        }

        // Only owner or admin can edit
        if ($expense->user_id != $uid && ! $this->isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $this->form_validation->set_rules('title',        'Title',    'required|trim|max_length[200]');
        $this->form_validation->set_rules('amount',       'Amount',   'required|decimal|greater_than[0]');
        $this->form_validation->set_rules('expense_date', 'Date',     'required');
        $this->form_validation->set_rules('category_id',  'Category', 'required|integer');
        $this->form_validation->set_rules('currency',     'Currency', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->jsonResponse(['success' => false, 'errors' => $this->form_validation->error_array()]);
        }

        $this->Expense_model->update($id, [
            'category_id'  => $this->input->post('category_id'),
            'title'        => $this->input->post('title', TRUE),
            'amount'       => $this->input->post('amount'),
            'currency'     => $this->input->post('currency'),
            'expense_date' => $this->input->post('expense_date'),
            'note'         => $this->input->post('note', TRUE),
        ]);

        $this->jsonResponse(['success' => true, 'message' => 'Expense updated successfully.']);
    }

    // POST /expenses/delete/:id (AJAX)
    public function delete($id)
    {
        $this->requireAjax();
        $uid  = $this->session->userdata('user_id');

        $expense = $this->Expense_model->getOne($id);
        if ( ! $expense) {
            $this->jsonResponse(['success' => false, 'message' => 'Expense not found.'], 404);
        }
        if ($expense->user_id != $uid && ! $this->isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $this->Expense_model->delete($id);
        $this->jsonResponse(['success' => true, 'message' => 'Expense deleted.']);
    }

    // GET /expenses/get/:id (AJAX - for edit modal)
    public function get($id)
    {
        $this->requireAjax();
        $uid  = $this->session->userdata('user_id');
        $expense = $this->Expense_model->getOne($id);
        if ( ! $expense || ($expense->user_id != $uid && ! $this->isAdmin())) {
            $this->jsonResponse(['success' => false], 404);
        }
        $this->jsonResponse(['success' => true, 'expense' => $expense]);
    }
}
