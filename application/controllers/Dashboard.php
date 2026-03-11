<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->load->model(['Expense_model', 'ExchangeRate_model', 'User_model']);
    }

    public function index()
    {
        $uid   = $this->session->userdata('user_id');
        $role  = $this->session->userdata('user_role');
        $year  = date('Y');
        $month = date('m');

        // Display currency (from query param or user preference)
        $displayCurrency = $this->input->get('currency') ?: ($this->User_model->getById($uid)->currency ?? 'INR');
        $baseCurrency    = 'INR';

        // Category access
        $accessIds = $this->User_model->getAccessibleCategoryIds($uid, $role);

        // Exchange rate map
        $rateMap = $this->ExchangeRate_model->getRateMap($baseCurrency);
        $rate    = $rateMap[$displayCurrency] ?? 1.0;

        $raw_month = (float)$this->Expense_model->totalByMonth($uid, $year, $month, $accessIds);
        $raw_year  = (float)$this->Expense_model->totalByYear($uid, $year, $accessIds);
        $raw_all   = (float)$this->Expense_model->totalAll($uid, $accessIds);

        $data['user']             = $this->currentUser();
        $data['title']            = 'Dashboard';
        $data['display_currency'] = $displayCurrency;
        $data['rate']             = $rate;
        $data['total_month']      = $raw_month * $rate;
        $data['total_year']       = $raw_year  * $rate;
        $data['total_all']        = $raw_all   * $rate;
        $data['by_category']      = $this->Expense_model->totalByCategory($uid, $year, $month, $accessIds);
        $data['recent']           = $this->Expense_model->recent($uid, 5, $accessIds);
        $data['monthly_chart']    = array_map(fn($v) => round($v * $rate, 2), $this->Expense_model->monthlyChart($uid, $year, $accessIds));
        $data['rate_map']         = $rateMap;

        $this->db->select('code, name, symbol')->from('currencies')->order_by('code');
        $data['currencies'] = $this->db->get()->result();

        $this->render('dashboard/index', $data);
    }
}
