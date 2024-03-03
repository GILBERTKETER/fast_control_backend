<?php

class ApplicationModel extends CI_Model {
    public function getAllApplications() {
        $query = $this->db->get('applications');

        return $query->result();
    }

    public function addApplication($jsonData) {
        $this->db->set('application', $jsonData);
        $this->db->insert('applications');
        return $this->db->insert_id();
    }

    public function getApplication($appId) {
        $this->db->where('id', $appId);
        $query = $this->db->get('applications');
        return $query->row();
    }

    public function updateApplication($appId, $jsonData) {
        try {
            $data = array(
                'application' => $jsonData
            );
            $this->db->where('id', $appId);
            $this->db->update('applications', $data);
            return true;
        } catch(Exception $err) {
            return false;
        }
    }

    public function deleteApplication($appId) {
        try {
            $this->db->where('id', $appId);
            $this->db->delete('applications');

            return true;
        } catch(Exception $err) {
            return false;
        }
    }

    public function deleteAllApplications() {
        $this->db->empty_table('applications');

        return true;
    }
}