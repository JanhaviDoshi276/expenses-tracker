<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->load->model(['Expense_model', 'Category_model', 'User_model', 'ExchangeRate_model']);
    }

    public function index()
    {
        $uid  = $this->session->userdata('user_id');
        $role = $this->session->userdata('user_role');
        $data['user']       = $this->currentUser();
        $data['title']      = 'Export Expenses';
        $data['categories'] = $this->Category_model->getAccessible($uid, $role);
        $this->db->select('code, name, symbol')->from('currencies')->order_by('code');
        $data['currencies'] = $this->db->get()->result();
        $this->render('exports/index', $data);
    }

    public function csv()
    {
        $uid      = $this->session->userdata('user_id');
        $role     = $this->session->userdata('user_role');
        $filters  = $this->_getFilters();
        $accessIds = $this->User_model->getAccessibleCategoryIds($uid, $role);
        $expenses = $this->Expense_model->getFiltered($uid, $filters, $accessIds);
        $displayCurrency = $this->input->get('display_currency') ?: 'INR';
        $rateMap  = $this->ExchangeRate_model->getRateMap('INR');
        $rate     = $rateMap[$displayCurrency] ?? 1.0;

        $filename = 'expenses_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['#', 'Title', 'Category', 'Amount (' . $displayCurrency . ')', 'Original Amount', 'Currency', 'Date', 'Note', 'Added By', 'Added On']);
        foreach ($expenses as $i => $e) {
            $converted = round($e->amount * $rate, 2);
            fputcsv($out, [
                $i + 1,
                $e->title,
                $e->category_name,
                $converted,
                $e->amount,
                $e->currency,
                $e->expense_date,
                $e->note,
                $e->added_by_name,
                $e->created_at,
            ]);
        }
        fclose($out);
        exit;
    }

    public function pdf()
    {
        $uid      = $this->session->userdata('user_id');
        $role     = $this->session->userdata('user_role');
        $filters  = $this->_getFilters();
        $accessIds = $this->User_model->getAccessibleCategoryIds($uid, $role);
        $expenses = $this->Expense_model->getFiltered($uid, $filters, $accessIds);
        $displayCurrency = $this->input->get('display_currency') ?: 'INR';
        $rateMap  = $this->ExchangeRate_model->getRateMap('INR');
        $rate     = $rateMap[$displayCurrency] ?? 1.0;
        $total    = array_sum(array_map(fn($e) => $e->amount * $rate, $expenses));
        $profile  = $this->User_model->getById($uid);

        $dompdfPath = APPPATH . '../vendor/dompdf/dompdf/src/Dompdf.php';
        if (file_exists($dompdfPath)) {
            require_once APPPATH . '../vendor/autoload.php';
            $dompdf = new \Dompdf\Dompdf();
            ob_start();
            $this->load->view('exports/pdf_print', compact('expenses','total','filters','displayCurrency','rate','profile'));
            $html = ob_get_clean();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream('expenses_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
            exit;
        } else {
            $this->load->view('exports/pdf_print', compact('expenses','total','filters','displayCurrency','rate','profile'));
        }
    }

    private function _getFilters()
    {
        return [
            'category_id' => $this->input->get('category_id'),
            'month'       => $this->input->get('month'),
            'year'        => $this->input->get('year') ?: date('Y'),
            'search'      => $this->input->get('search', TRUE),
        ];
    }
}
