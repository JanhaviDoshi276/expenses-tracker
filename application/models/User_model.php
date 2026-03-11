<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'users';

    public function getByEmail($email)
    {
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function getAll($search = '')
    {
        $this->db->select('id, name, email, role, status, currency, created_at');
        $this->db->from($this->table);
        if ($search) {
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
        }
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function updateProfile($id, array $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function updatePassword($id, $hashedPassword)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, ['password' => $hashedPassword]);
        return $this->db->affected_rows();
    }

    public function delete($id)
    {
        // Cannot delete superadmin
        $this->db->where('id', $id);
        $this->db->where('role !=', 'superadmin');
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    public function emailExistsForOther($email, $excludeId)
    {
        return $this->db
            ->where('email', $email)
            ->where('id !=', $excludeId)
            ->count_all_results($this->table) > 0;
    }

    // Category access
    public function getCategoryAccess($userId)
    {
        return $this->db
            ->select('category_id')
            ->get_where('user_category_access', ['user_id' => $userId])
            ->result_array();
    }

    public function getAccessibleCategoryIds($userId, $role)
    {
        if ($role === 'superadmin') return null; // null = all
        $rows = $this->getCategoryAccess($userId);
        return array_column($rows, 'category_id');
    }

    public function setCategoryAccess($userId, array $categoryIds)
    {
        $this->db->delete('user_category_access', ['user_id' => $userId]);
        if ( ! empty($categoryIds)) {
            $inserts = [];
            foreach ($categoryIds as $cid) {
                $inserts[] = ['user_id' => $userId, 'category_id' => (int)$cid];
            }
            $this->db->insert_batch('user_category_access', $inserts);
        }
    }

    /** Returns assoc array: [user_id => [cat_ids...]] for all users */
    public function getAllCategoryAccessMap()
    {
        $rows = $this->db->get('user_category_access')->result();
        $map  = [];
        foreach ($rows as $r) {
            $map[$r->user_id][] = (int)$r->category_id;
        }
        return $map;
    }

    public function count()
    {
        return $this->db->count_all($this->table);
    }
}
