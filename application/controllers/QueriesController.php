<?php
class QueriesController extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model("QueryModel");

        $this->load->library('cors');
        $this->cors->handle();
    }

    public function getAllQueries() {
        try {
            $rows = $this->QueryModel->getAllQueries();

            if ($rows) {
                $returnValue = array('msg' => 'success', 'allQueries' => $rows);
            } else {
                $returnValue = array('msg' => 'failed', 'allQueries' => null);
            }

            echo json_encode($returnValue);
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function getQuery() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $row = $this->QueryModel->getQuery($payload->id);

            if ($row) {
                $returnValue = array('msg' => 'success', 'queryData' => $row);
            } else {
                $returnValue = array('msg' => 'failed', 'queryData' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function addQuery() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $insertId = $this->QueryModel->addQuery($jsonPayload);

            if ($insertId) {
                $returnValue = array('msg' => 'success', 'queryId' => ''.$insertId);
            } else {
                $returnValue = array('msg' => 'failed', 'queryId' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function saveQuery() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $queryJSON = array(
                'queryJSON' => $payload->queryJSON
            );
            $result = $this->QueryModel->updateQuery($payload->id, json_encode($queryJSON));

            if ($result) {
                $returnValue = array('msg' => 'success');
            } else {
                $returnValue = array('msg' => 'failed');
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function deleteQuery() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $result = $this->QueryModel->deleteQuery($payload->id);

            if ($result) {
                $returnValue = array('msg' => 'success');
            } else {
                $returnValue = array('msg' => 'failed');
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function deleteAllQueries() {
        try {
            $result = $this->QueryModel->deleteAllQueries();

            if ($result) {
                $returnValue = array('msg' => 'success');
            } else {
                $returnValue = array('msg' => 'failed');
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }
}