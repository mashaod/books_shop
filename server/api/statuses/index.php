<?php
include ('../../utils/Rest.php');

Class Statuses extends Rest
{
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getStatuses()
        {
            $sth = $this->pdo->query("SELECT id, name FROM statuses");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postStatuses()
        {      
            if ($this->params['name_status'])
            {
                $nameStatus = $this->params['name_status'];
                $sth = $this->pdo->prepare("INSERT INTO statuses (name) VALUES ('$nameStatus')");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Invalid request POST', 404);
            }
            else
                $this->createResponse('I need name_status', 404);
        }

        public function putStatuses()
        {
            if (Validator::checkParams($this->params, array('id', 'name_status')))
            {
                $idPayment = $this->params['id'];
                $namePayment = $this->params['name_status'];                   
                
                $query = "UPDATE statuses SET name = '$namePayment' WHERE `id` = '$idPayment'";
                $sth = $this->pdo->prepare($query);
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success PUT payment', 202);
                else
                    $this->createResponse('Incorrect request PUT payment', 404);
            }
            else
                $this->createResponse(ERR_105, 404);
        }

        public function deleteStatuses()
        {
            list($id) = explode('/', $this->params);
            $idStatus = Validator::checkId($id)?$id:false;

            if ($idStatus)
            {
                $sth = $this->pdo->prepare("DELETE FROM statuses WHERE id = '$idStatus'");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                     $this->createResponse('Success DELETE status', 202);
                else
                    $this->createResponse('We didn\'t find this status', 404);
            }
            else
                $this->createResponse('I need id of status', 404);
        }

        public function getStatusesByParams()
        {
            list($params['id'],$params['name']) = explode('/', $this->params, 2);
            $params = Validator::clearData($params);

            if (count($params)>0)
            {
                foreach ($params as $key=>$val)
                {
                    if ($key == 'name')
                    {
                        $operand = ' LIKE ';
                        $val = '%' . $val . '%';
                    }
                    else
                        $operand = '=';

                    $where[] = $key . $operand . '\'' . $val . '\' ';
                }
                
                $where = join('AND ', $where);
                $sth = $this->pdo->query("SELECT id, name FROM `statuses` where $where");

                if ($sth)
                {
                    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

                    if(count($data)>0)
                        $this->createResponse($data, 200);
                    else
                        $this->createResponse(ERR_111, 404);
                }
                else
                    $this->createResponse(ERR_110, 404);
            }
            else
                $this->getPayments();
        }
}

$obj = new Statuses();
$obj->start();

