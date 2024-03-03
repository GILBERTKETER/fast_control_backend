<?php
class GraphModel extends CI_Model {
    public function getAllGraphs() {
        $query = $this->db->get('table_graphs');
        return $query->result();
    }

    public function addGraphData($jsonData) {
        $this->db->set('graph', $jsonData); // Replace 'column_name' with the actual column name in the 'table_graphs' table
        $this->db->insert('table_graphs');
        return $this->db->insert_id();
    }

    public function getGraphData($graphId) {
        $this->db->where('id', $graphId);
        $query = $this->db->get('table_graphs');
        return $query->row();
    }

    public function updateGraphData($graphId, $jsonData) {
        $data = array(
            'graph' => $jsonData
        );
        $this->db->where('id', $graphId);
        $this->db->update('table_graphs', $data);
        return true;
    }

    public function deleteGraph($graphId) {
        $this->db->where('id', $graphId);
        $this->db->delete('table_graphs');
        return true;
    }
    
    public function deleteAllGraphs() {
        $this->db->empty_table('table_graphs');
        return true;
    }
}