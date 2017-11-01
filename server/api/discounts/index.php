<?php
include ('../../utils/Rest.php');

Class Discounts extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getDiscounts()
        {
            $sth = $this->pdo->query("SELECT id, name, discount FROM discounts");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postDiscounts()
        {
            if (Validator::checkParams($this->params, array('name_discount', 'percen_discount')))
            {
                $nameDiscount = $this->params['name_discount'];
                $percenDiscount = $this->params['percen_discount']; 

                $sth = $this->pdo->prepare("INSERT INTO discounts (name, discount) VALUES ('$nameDiscount', '$percenDiscount')");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Invalid request POST', 404);
            }
            else
                $this->createResponse('I need params name_discount, percen_discount', 404);
        }

        public function putDiscounts()
        {
            if (Validator::checkParams($this->params, array('id', 'name_discount', 'percen_discount')))
            {
                $idDiscount = $this->params['id'];
                $nameDiscount = $this->params['name_discount'];
                $percenDiscount = $this->params['percen_discount'];
                
                $query = "UPDATE discounts SET name = '$nameDiscount', discount = '$percenDiscount' WHERE id = '$idDiscount'";
                $sth = $this->pdo->prepare($query);
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success PUT discount', 202);
                else
                    $this->createResponse('Incorrect request PUT discount', 404);
            }
            else
                $this->createResponse('I need params id, name_discount, percen_discount', 404);
        }

        public function deleteDiscounts()
        {
            list($id) = explode('/', $this->params);
            $idDiscount = Validator::checkId($id)?$id:false;

            if ($idDiscount)
            {
                $sth = $this->pdo->prepare("DELETE FROM discounts WHERE id = '$idDiscount'");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                     $this->createResponse('Success DELETE discount', 202);
                else
                    $this->createResponse('We didn\'t find this discount', 404);
            }
            else
                $this->createResponse('I need id of discount', 404);
        }

        public function getDiscountsByParams()
        {
            list($params['id'],$params['name'], $params['discount']) = explode('/', $this->params, 3);
            $params = Validator::clearData($params);

            if (count($params)>0)
            {
                foreach ($params as $key=>$val)
                {
                    if($key == 'name')
                    {
                        $operand = ' LIKE ';
                        $val = '%' . $val . '%';
                    }
                    else
                        $operand = '=';

                    $where[] = $key . $operand . '\'' . $val . '\' ';
                }
                
                $where = join('AND ', $where);
                $sth = $this->pdo->query("SELECT id, name, discount FROM discounts where $where");

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
                $this->getDiscounts();
        }
    }

$obj = new Discounts();
$obj->start();

