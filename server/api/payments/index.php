<?php
include ('../../utils/Rest.php');

Class Payments extends Rest
{
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getPayments()
        {
            $sth = $this->pdo->query("SELECT id, name FROM payments");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postPayments()
        {
            if ($this->params['name_payment'])
            {
                $namePayment = $this->params['name_payment'];
                $sth = $this->pdo->prepare("INSERT INTO payments (name) VALUES ('$namePayment')");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Invalid request POST', 404);
            }
            else
                $this->createResponse('I need name_payment', 404);
        }

        public function putPayments()
        {
            if (Validator::checkParams($this->params, array('id', 'name_payment')))
            {
                $idPayment = $this->params['id'];
                $namePayment = $this->params['name_payment'];

                $query = "UPDATE payments SET name = '$namePayment' WHERE `id` = '$idPayment'";
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

        public function deletePayments()
        {
            list($id) = explode('/', $this->params);
            $idPayment = Validator::checkId($id)?$id:false;

            if ($idPayment)
            {
                $sth = $this->pdo->prepare("DELETE FROM payments where id = '$idPayment'");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                     $this->createResponse('Success DELETE payment', 202);
                else
                    $this->createResponse('We didn\'t find this payment', 404);
            }
            else
                $this->createResponse('I need id of payment', 404);
        }

        public function getPaymentsByParams()
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
                $sth = $this->pdo->query("SELECT id, name FROM `payments` where $where");

                if ($sth)
                {
                    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

                    if (count($data)>0)
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

$obj = new Payments();
$obj->start();

