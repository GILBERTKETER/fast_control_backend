<?php
class QueryModel extends CI_Model {
    public function getAllQueries() {
        $query = $this->db->get('queries');

        return $query->result();
    }

    public function addQuery($jsonData) {
        $this->db->set('query', $jsonData);
        $this->db->insert('queries');

        return $this->db->insert_id();
    }

    public function getQuery($queryId) {
        $this->db->where('id', $queryId);
        
        $query = $this->db->get('queries');

        return $query->row();
    }

    public function updateQuery($queryId, $jsonData) {
        try {
            $data = array(
                'query' => $jsonData
            );

            $this->db->where('id', $queryId);
            $this->db->update('queries', $data);

            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function deleteQuery($queryId) {
        try {
            $this->db->where('id', $queryId);
            $this->db->delete('queries');

            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function deleteAllQueries() {
        $this->db->empty_table('queries');

        return true;
    }
}