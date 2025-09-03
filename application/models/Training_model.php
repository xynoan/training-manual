<?php
class Training_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    // operations: insert, update, delete, get

    public function insert_training($data)
    {
        $training_data = array(
            'title' => $data['title']
        );

        $this->db->insert('tbl_training_manual', $training_data);
        $manual_id = $this->db->insert_id();

        $file_name = is_array($data['name']) ? implode(', ', $data['name']) : $data['name'];

        $files = array(
            'manual_id' => $manual_id,
            'file_name' => $file_name,
            'file_path' => 'hard-coded for now'
        );
        $this->db->insert('tbl_training_manual_file', $files);

        if (isset($data['note']) && !empty($data['note'])) {
            $notes_data = array(
                'manual_id' => $manual_id,
                'note' => $data['note']
            );
            $this->db->insert('tbl_training_manual_notes', $notes_data);
        }

        return $manual_id;
    }

    public function update_training($id, $data)
    {
        $training_data = array(
            'title' => $data['title']
        );

        $this->db->where('id', $id);
        $this->db->update('tbl_training_manual', $training_data);

        if (isset($data['name']) && !empty($data['name'])) {
            $file_name = is_array($data['name']) ? implode(', ', $data['name']) : $data['name'];

            $files = array(
                'file_name' => $file_name,
                'file_path' => 'hard-coded for now'
            );

            $this->db->where('manual_id', $id);
            $this->db->update('tbl_training_manual_file', $files);
        }

        if (isset($data['note'])) {
            $notes_data = array(
                'note' => $data['note']
            );

            $this->db->where('manual_id', $id);
            $this->db->update('tbl_training_manual_notes', $notes_data);
        }

        return true;
    }

    public function delete_training($id)
    {
        return $this->db->delete('tbl_training_manual', ['id' => $id]);
    }

    public function get_training_by_id($id)
    {
        $this->db->select('
        tm.id, 
        tm.title, 
        GROUP_CONCAT(tmf.file_name) AS file_names, 
        tm.created_by, 
        tm.created_at, 
        tmn.note
    ');
        $this->db->from('tbl_training_manual tm');
        $this->db->join('tbl_training_manual_file tmf', 'tm.id = tmf.manual_id', 'left');
        $this->db->join('tbl_training_manual_notes tmn', 'tm.id = tmn.manual_id', 'left');
        $this->db->where('tm.id', $id);
        $this->db->group_by('tm.id');
        $this->db->order_by('tm.id', 'ASC');
        $query = $this->db->get();
        $result = $query->row_array();
        if ($result) {
            $result['file_names'] = $result['file_names']
                ? explode(',', $result['file_names'])
                : [];
        }
        return $result;
    }

    public function get_all_trainings_paginated($limit, $offset)
    {
        $this->db->select('
        tm.id, 
        tm.title, 
        GROUP_CONCAT(tmf.file_name) AS file_names, 
        tm.created_by, 
        tm.created_at, 
        tmn.note
    ');
        $this->db->from('tbl_training_manual tm');
        $this->db->join('tbl_training_manual_file tmf', 'tm.id = tmf.manual_id', 'left');
        $this->db->join('tbl_training_manual_notes tmn', 'tm.id = tmn.manual_id', 'left');
        $this->db->group_by('tm.id');
        $this->db->order_by('tm.id', 'ASC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $results = $query->result_array();

        foreach ($results as &$row) {
            $row['file_names'] = $row['file_names']
                ? explode(',', $row['file_names'])
                : [];
        }

        return $results;
    }

    public function count_all_trainings()
    {
        return $this->db->count_all('tbl_training_manual');
    }
}
