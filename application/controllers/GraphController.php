<?php
class GraphController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('GraphModel');

        $this->load->library('cors');
        $this->cors->handle();
    }

    public function getAllGraphs() {
        $jsonPayload = $this->input->raw_input_stream;
        $payload = json_decode($jsonPayload);
        $rows = $this->GraphModel->getAllGraphs();
        // $row = $this->GraphModel->getGraphData(20);
        
        if ($rows) {
            $returnValue = array('msg'=>'success', 'allGraphsData'=>$rows );
        } else {
            $returnValue = array('msg'=>'success', 'allGraphsData'=>[] );
        }
        echo json_encode($returnValue);
    }

    public function getGraph() {
        $jsonPayload = $this->input->raw_input_stream;
        $payload = json_decode($jsonPayload);
        $row = $this->GraphModel->getGraphData($payload->id);
        // $row = $this->GraphModel->getGraphData(20);
        
        if ($row) {
            $returnValue = array('msg'=>'success', 'graphData'=>$row);
        } else {
            $returnValue = array('msg'=>'failed');
        }
        echo json_encode($returnValue);
    }

    public function addGraph() {
        $jsonPayload = $this->input->raw_input_stream;
        // $payload = json_decode($jsonPayload);
        $insertId = $this->GraphModel->addGraphData($jsonPayload);
        
        if ($insertId) {
            $returnValue = array('msg'=>'success', 'graphID'=>''.$insertId);
        } else {
            $returnValue = array('msg'=>'failed');
        }
        echo json_encode($returnValue);
    }

    public function saveGraph() {
        $jsonPayload = $this->input->raw_input_stream;
        $payload = json_decode($jsonPayload);
        $graphJSON = array(
            'graphJSON' => $payload->graphJSON
        );
        $result = $this->GraphModel->updateGraphData($payload->id, json_encode($graphJSON));
        
        if ($result) {
            $returnValue = array('msg'=>'success');
        } else {
            $returnValue = array('msg'=>'failed');
        }
        echo json_encode($returnValue);
    }

    public function deleteGraph() {
        $jsonPayload = $this->input->raw_input_stream;
        $payload = json_decode($jsonPayload);
        $result = $this->GraphModel->deleteGraph($payload->id);
        if ($result) {
            $returnValue = array('msg'=>'success');
        } else {
            $returnValue = array('msg'=>'failed');
        }
        echo json_encode($returnValue);
    }

    public function deleteAllGraphs() {
        $result = $this->GraphModel->deleteAllGraphs();
        if ($result) {
            $returnValue = array('msg'=>'success');
        } else {
            $returnValue = array('msg'=>'failed');
        }
        echo json_encode($returnValue);
    }
}