<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    protected $table = 'categories';

    public function getAll($search = '')
    {
        $this->db->select('*')->from($this->table);
        if ($search) {
            $this->db->like('name', $search);
        }
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    public function getAccessible($userId, $role, $search = '')
    {
        $this->db->select('c.*')->from('categories c');
        if ($role !== 'superadmin') {
            $this->db->join('user_category_access uca', 'uca.category_id = c.id');
            $this->db->where('uca.user_id', $userId);
        }
        if ($search) {
            $this->db->like('c.name', $search);
        }
        $this->db->order_by('c.is_default', 'DESC');
        $this->db->order_by('c.name', 'ASC');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function getDefault()
    {
        return $this->db->get_where($this->table, ['is_default' => 1])->row();
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

    public function delete($id)
    {
        // Find the default "Unspecified" category
        $default = $this->getDefault();
        $defaultId = $default ? $default->id : 1;

        // Reassign all expenses from this category to default
        $this->db->where('category_id', $id)
                 ->update('expenses', ['category_id' => $defaultId]);

        // Remove category access entries
        $this->db->delete('user_category_access', ['category_id' => $id]);

        // Delete the category
        $this->db->where('id', $id)->where('is_default', 0)->delete($this->table);
        return $this->db->affected_rows();
    }

    public function count()
    {
        return $this->db->count_all($this->table);
    }
}
