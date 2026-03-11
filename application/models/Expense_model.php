<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {

    protected $table = 'expenses';

    public function getOne($id, $userId = null)
    {
        $this->db->select('e.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                           u.name AS owner_name, ab.name AS added_by_name, cu.symbol AS currency_symbol')
                 ->from('expenses e')
                 ->join('categories c', 'c.id = e.category_id')
                 ->join('users u', 'u.id = e.user_id')
                 ->join('users ab', 'ab.id = e.added_by')
                 ->join('currencies cu', 'cu.code = e.currency', 'left')
                 ->where('e.id', $id);
        if ($userId !== null) {
            $this->db->where('e.user_id', $userId);
        }
        return $this->db->get()->row();
    }

    public function create(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data, $userId = null)
    {
        $this->db->where('id', $id);
        if ($userId !== null) {
            $this->db->where('user_id', $userId);
        }
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete($id, $userId = null)
    {
        $this->db->where('id', $id);
        if ($userId !== null) {
            $this->db->where('user_id', $userId);
        }
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    public function getFiltered($userId, array $f, $accessibleCategoryIds = null)
    {
        $this->_applyFilters($userId, $f, $accessibleCategoryIds);
        return $this->db
            ->select('e.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                      u.name AS owner_name, ab.name AS added_by_name, cu.symbol AS currency_symbol')
            ->from('expenses e')
            ->join('categories c', 'c.id = e.category_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users ab', 'ab.id = e.added_by')
            ->join('currencies cu', 'cu.code = e.currency', 'left')
            ->order_by('e.expense_date', 'DESC')
            ->order_by('e.id', 'DESC')
            ->get()->result();
    }

    public function getAllFiltered(array $f, $accessibleCategoryIds = null)
    {
        $this->_applyAdminFilters($f, $accessibleCategoryIds);
        return $this->db
            ->select('e.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color,
                      u.name AS owner_name, ab.name AS added_by_name, cu.symbol AS currency_symbol')
            ->from('expenses e')
            ->join('categories c', 'c.id = e.category_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users ab', 'ab.id = e.added_by')
            ->join('currencies cu', 'cu.code = e.currency', 'left')
            ->order_by('e.expense_date', 'DESC')
            ->order_by('e.id', 'DESC')
            ->get()->result();
    }

    public function filteredTotal($userId, array $f, $accessibleCategoryIds = null)
    {
        $this->_applyFilters($userId, $f, $accessibleCategoryIds);
        // Must join users ab because _applyCommonFilters may reference ab.name in search
        $row = $this->db
            ->select_sum('e.amount', 'amount')
            ->from('expenses e')
            ->join('users ab', 'ab.id = e.added_by', 'left')
            ->get()->row();
        return $row->amount ?? 0;
    }

    private function _applyFilters($userId, array $f, $accessibleCategoryIds = null)
    {
        $this->db->where('e.user_id', $userId);
        $this->_applyCommonFilters($f, $accessibleCategoryIds);
    }

    private function _applyAdminFilters(array $f, $accessibleCategoryIds = null)
    {
        if ( ! empty($f['user_id'])) {
            $this->db->where('e.user_id', $f['user_id']);
        }
        $this->_applyCommonFilters($f, $accessibleCategoryIds);
    }

    private function _applyCommonFilters(array $f, $accessibleCategoryIds = null)
    {
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) {
                $this->db->where('1=0'); // no access
            } else {
                $this->db->where_in('e.category_id', $accessibleCategoryIds);
            }
        }
        if ( ! empty($f['category_id'])) {
            $this->db->where('e.category_id', $f['category_id']);
        }
        if ( ! empty($f['year'])) {
            $this->db->where('YEAR(e.expense_date)', $f['year']);
        }
        if ( ! empty($f['month'])) {
            $this->db->where('MONTH(e.expense_date)', $f['month']);
        }
        if ( ! empty($f['search'])) {
            $this->db->group_start();
            $this->db->like('e.title', $f['search']);
            $this->db->or_like('ab.name', $f['search']);
            $this->db->group_end();
        }
        if ( ! empty($f['currency'])) {
            $this->db->where('e.currency', $f['currency']);
        }
    }

    // Dashboard aggregates
    public function totalByMonth($userId, $year, $month, $accessibleCategoryIds = null)
    {
        $this->db->where('user_id', $userId)
                 ->where('YEAR(expense_date)', $year)
                 ->where('MONTH(expense_date)', $month);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) return 0;
            $this->db->where_in('category_id', $accessibleCategoryIds);
        }
        $row = $this->db->select_sum('amount')->get($this->table)->row();
        return $row->amount ?? 0;
    }

    public function totalByYear($userId, $year, $accessibleCategoryIds = null)
    {
        $this->db->where('user_id', $userId)->where('YEAR(expense_date)', $year);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) return 0;
            $this->db->where_in('category_id', $accessibleCategoryIds);
        }
        $row = $this->db->select_sum('amount')->get($this->table)->row();
        return $row->amount ?? 0;
    }

    public function totalAll($userId, $accessibleCategoryIds = null)
    {
        $this->db->where('user_id', $userId);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) return 0;
            $this->db->where_in('category_id', $accessibleCategoryIds);
        }
        $row = $this->db->select_sum('amount')->get($this->table)->row();
        return $row->amount ?? 0;
    }

    public function totalByCategory($userId, $year, $month, $accessibleCategoryIds = null)
    {
        $this->db->select('c.name, c.icon, c.color, SUM(e.amount) AS total')
                 ->from('expenses e')
                 ->join('categories c', 'c.id = e.category_id')
                 ->where('e.user_id', $userId)
                 ->where('YEAR(e.expense_date)', $year)
                 ->where('MONTH(e.expense_date)', $month);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) return [];
            $this->db->where_in('e.category_id', $accessibleCategoryIds);
        }
        return $this->db->group_by('e.category_id')->order_by('total', 'DESC')->get()->result();
    }

    public function recent($userId, $limit = 5, $accessibleCategoryIds = null)
    {
        $this->db->select('e.*, c.name AS category_name, c.icon AS category_icon, c.color AS category_color, cu.symbol AS currency_symbol')
                 ->from('expenses e')
                 ->join('categories c', 'c.id = e.category_id')
                 ->join('currencies cu', 'cu.code = e.currency', 'left')
                 ->where('e.user_id', $userId);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) return [];
            $this->db->where_in('e.category_id', $accessibleCategoryIds);
        }
        return $this->db->order_by('e.expense_date', 'DESC')
                        ->order_by('e.id', 'DESC')
                        ->limit($limit)
                        ->get()->result();
    }

    public function monthlyChart($userId, $year, $accessibleCategoryIds = null)
    {
        $this->db->where('user_id', $userId)->where('YEAR(expense_date)', $year);
        if ($accessibleCategoryIds !== null) {
            if (empty($accessibleCategoryIds)) {
                return array_values(array_fill(0, 12, 0));
            }
            $this->db->where_in('category_id', $accessibleCategoryIds);
        }
        $rows = $this->db->select('MONTH(expense_date) AS m, SUM(amount) AS total')
                         ->group_by('MONTH(expense_date)')
                         ->get($this->table)->result();
        $months = array_fill(1, 12, 0);
        foreach ($rows as $r) {
            $months[(int)$r->m] = (float)$r->total;
        }
        return array_values($months);
    }

    public function count()
    {
        return $this->db->count_all($this->table);
    }
}
