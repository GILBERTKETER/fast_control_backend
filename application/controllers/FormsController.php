<?php
class FormsController extends CI_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->load->model("FormModel");
        $this->load->model("ApplicationModel");

        $this->load->library('cors');
        $this->cors->handle();

        $this->load->helper('url');
    }

    public function getAllForms() {
        try {
            // $jsonPayload = $this->input->raw_input_stream;
            $rows = $this->FormModel->getAllForms();

            if ($rows) {
                $returnValue = array('msg' => 'success', 'allForms' => $rows);
            } else {
                $returnValue = array('msg' => 'failed', 'allForms' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function getForm() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            
            $row = $this->FormModel->getForm($payload->id);

            if ($row) {
                $returnValue = array('msg' => 'success', 'formData' => $row);
            } else {
                $returnValue = array('msg' => 'failed', 'formData' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function addForm() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $insertId = $this->FormModel->addForm($jsonPayload);

            if ($insertId) {
                $returnValue = array('msg' => 'success', 'formId' => ''.$insertId);
            } else {
                $returnValue = array('msg' => 'failed', 'formId' => null);
            }

            echo json_encode($returnValue);
        } catch(Exception $e) {
            echo $e;
        }
    }

    public function saveForm() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $formJSON = array(
                'formJSON' => $payload->formJSON
            );
            $result = $this->FormModel->updateForm($payload->id, json_encode($formJSON));

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

    public function deleteForm() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $result = $this->FormModel->deleteForm($payload->id);

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

    public function deleteAllForms() {
        try {
            $jsonPayload = $this->input->raw_input_stream;
            $payload = json_decode($jsonPayload);
            $result = $this->FormModel->deleteAllForms();

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

    public function submitForm() {
        try {
            $formPayload = $this->input->post();
            $formId = $this->input->get('formId');
            $tableName = $this->input->get('tableName');
            $redirectURI = $this->input->get('redirect');

// echo "formId: ".$formId;
            $targetForm = json_decode($this->FormModel->getForm($formId)->form);

            // echo $targetForm->formJSON->appId;

            $targetApp = json_decode($this->ApplicationModel->getApplication($targetForm->formJSON->appId)->application);

            $appConnectionConfig = $targetApp->appJSON->connectionConfig;


            $database_config = array(
                'dsn'      => '',
                'hostname' => $appConnectionConfig->hostname,
                'username' => $appConnectionConfig->username,
                'password' => $appConnectionConfig->password,
                'database' => $appConnectionConfig->database,
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => FALSE,
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
                'swap_pre' => '',
                'encrypt'  => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
            );

            $targetDB = $this->load->database($database_config, TRUE);

// $this->input->get('u_name')

            $fieldNames = array();
            $fieldValues = array();
            $parsedfields = array();

            foreach($formPayload as $key => $val)
            {
                $fieldNames[] = "`".$key."`";
                $fieldValues[] = "`".$val."`";
            }

            $columns = $targetDB->list_fields($tableName);

            if ($columns === false) {
                echo "unable to fetch columns";
                return;
            }

            foreach($columns as $col) {
                // $parsedfields[] = $formPayload[];
                // $targetFormVal = $formPayload[$col];

                if (array_key_exists($col, $formPayload)) {
                    $parsedfields[$col] = $formPayload[$col];
                } else {
                    $parsedfields[$col] = null;
                }

                // echo $targetFormVal;
            }

            $fieldsStmt = array();
            $valuesStmt = array();

            foreach($parsedfields as $key => $val)
            {
                // echo $key." => ".$val;
                $fieldsStmt[] = "`".$key."`";
                if ($val) {
                    $valuesStmt[] = "'".$val."'";
                } else {
                    $valuesStmt[] = 'NULL';
                }
            }
            
            // INSERT INTO `posts` (`post_id`, `title`, `caption`, `user_id`) VALUES (NULL, 'hello', 'asduas', NULL);
            // get the targetTable
            $query = "INSERT INTO `".$tableName."` (".implode(", ", $fieldsStmt)." ) VALUES ( ".implode(", ", $valuesStmt)." );";

            // echo;

            // echo $query;

            $query_result = $targetDB->query($query);

            // echo $query_result;

            // echo implode('\\n', $formPayload);

            // return redirect();

            // echo $redirectURI;
            $view_data = array(
                'redirectURI' => $redirectURI
            );  
            // $this->load->view('formSubmit', $view_data);

            return redirect($redirectURI);
        } catch(Exception $err) {
            echo $err;
        }
    }
}