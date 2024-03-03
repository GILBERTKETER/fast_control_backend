<?php
class ApplicationsController extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ApplicationModel');

        $this->load->library('cors');
        $this->cors->handle();
    }

    public function getAllApplications() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $rows = $this->ApplicationModel->getAllApplications();

            if ($rows) {
                $returnValue = array('msg' => 'success', 'allApplications' => $rows);
            } else {
                $returnValue = array('msg' => 'success', 'allApplications' => $rows);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function getApplication() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $row = $this->ApplicationModel->getApplication($payload->id);

            if ($row) {
                $returnValue = array('msg' => 'success', 'applicationData' => $row);
            } else {
                $returnValue = array('msg' => 'failed', 'applicationData' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function addApplication() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $insertId = $this->ApplicationModel->addApplication($jsonPayload);

            if ($insertId) {
                $returnValue = array('msg' => 'success', 'appId' => ''.$insertId);
            } else {
                $returnValue = array('msg' => 'failed', 'appId' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function saveApplication() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $appJSON = array(
                'appJSON' => $payload->appJSON
            );
            $result = $this->ApplicationModel->updateApplication($payload->id, json_encode($appJSON));

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

    public function deleteApplication() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $result = $this->ApplicationModel->deleteApplication($payload->id);

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

    public function deleteAllApplications() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $result = $this->ApplicationModel->deleteAllApplications();

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