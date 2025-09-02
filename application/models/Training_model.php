<?php
class Training_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function insert_training($data)
    {
        $training_data = array(
            'title' => $data['title']
        );

        $this->db->insert('tbl_training_manual', $training_data);
        $manual_id = $this->db->insert_id();

        $file_name = is_array($data['name']) ? implode(',', $data['name']) : $data['name'];

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
}
