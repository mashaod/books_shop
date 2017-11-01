<?php
include ('../../utils/Rest.php');

Class Carts extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getCarts()
        {
            $sth = $this->pdo->query("SELECT c.id_book, b.name, b.price, b.id_discount, c.count
                FROM carts AS c
                LEFT JOIN books AS b ON c.id_book=b.id");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postCarts()
        {
            if(Validator::checkParams($this->params, array('id_user', 'id_book', 'count')))
            {
                $idUser = $this->params['id_user'];          
                $idBook = $this->params['id_book']; 
                $count = $this->params['count'];

                $sth = $this->pdo->prepare("INSERT INTO carts (id_user, id_book, count) VALUES ('$idUser', '$idBook', '$count')");
                $sth->execute();

                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Incorrect request', 200);
            }
            else
                $this->createResponse('I need id_user, id_book and count', 200); 
        }

        public function putCarts()
        {
            if(Validator::checkParams($this->params, array('id', 'id_book', 'count')))
            {
                $idUser = $this->params['id'];
                $idBook = $this->params['id_book'];
                $count = $this->params['count'];

                $query = "UPDATE carts SET count = '$count' WHERE id_user = '$idUser' AND id_book = '$idBook'";
                $sth = $this->pdo->prepare($query);
                    
                if($sth->execute())
                    $this->createResponse(ERR_107, 202);
                else
                    $this->createResponse('Incorrect request', 404);
            }
            else
                $this->createResponse('I need id_user, id_book and count', 404);
        }

        public function deleteCarts()
        {
            list($idUser, $idBook) = explode('/', $this->params, 2);
            $idUser = Validator::checkId($idUser)?$idUser:false;
            $idGenre = Validator::checkId($idBook)?$idBook:false;

            if($idUser && $idBook)
                $sth = $this->pdo->prepare("DELETE FROM carts WHERE id_user = '$idUser' && id_book = '$idBook'");
            elseif($idUser)
                $sth = $this->pdo->prepare("DELETE FROM carts WHERE id_user = '$idUser'");
            else
                $sth = false;

            if ($sth)
            { 
                $sth->execute();

                if ($sth->rowCount() > 0)
                     $this->createResponse('Succsess', 202);
                 else
                     $this->createResponse('We didn\'t find this cart', 404);
            }
            else
                $this->createResponse('I need params id_user and id_books', 404);

        }

        public function getCartsByParams()
        {
            list($id) = explode('/', $this->params);
            $idUser = Validator::checkId($id)?$id:false;

            if ($idUser)
            {
                $sth = $this->pdo->query("SELECT c.id_book, b.name, b.price, d.discount, c.count
                    FROM carts AS c
                    LEFT JOIN books AS b ON c.id_book=b.id
                    LEFT JOIN discounts AS d ON d.id=b.id_discount
                    WHERE c.id_user = '$idUser'");

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
                $this->getCarts();
        }
    }

$obj = new Carts();
$obj->start();

