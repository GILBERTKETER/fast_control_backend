<?php

class FormModel extends CI_Model {
    public function getAllForms() {
        $query = $this->db->get('forms');

        return $query->result();
    }

    public function addForm($jsonData) {
        $this->db->set('form', $jsonData);
        $this->db->insert('forms');
        
        return $this->db->insert_id();
    }

    public function getForm($formId) {
        $this->db->where('id', $formId);
        $query = $this->db->get('forms');

        return $query->row();
    }

    public function updateForm($formId, $jsonData) {
        try {
            $data = array(
                'form' => $jsonData
            );
            $this->db->where('id', $formId);
            $this->db->update('forms', $data);

            return true;
        } catch(Exception $e) {
            echo $e;
            return false;
        }
    }

    public function deleteForm($formId) {
        try {
            $this->db->where('id', $formId);
            $this->db->delete('forms');

            return true;
        } catch(Exception $err) {
            return false;
        }
    }

    public function deleteAllForms() {
        $this->db->empty_table('forms');

        return true;
    }
}