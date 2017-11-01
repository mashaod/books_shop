<?php
include ('../../utils/Rest.php');

Class Authors extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getAuthors()
        {
            $sth = $this->pdo->query("SELECT id, name FROM authors");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postAuthors()
        {        
            if ($this->params['name_author'])
            {
                $nameAuthor = $this->params['name_author'];
                $sth = $this->pdo->prepare("INSERT INTO authors (name) VALUES ('$nameAuthor')");
                $sth->execute();

                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Invalid request POST', 404);
            }
            else
                $this->createResponse('I need name_author', 404);  
        }

        public function putAuthors()
        {
            if (Validator::checkParams($this->params, array('id_author', 'name_author')))
            {
                $idAuthor = $this->params['id_author'];
                $nameAuthor = $this->params['name_author'];

                $sth = $this->pdo->prepare("UPDATE authors SET name = '$nameAuthor' WHERE id = '$idAuthor'");
                $sth->execute();

                if ($sth->rowCount() > 0)
                    $this->createResponse('Success PUT authors', 202);
                else
                    $this->createResponse('Incorrect request PUT authors', 404);
            }
            else
                $this->createResponse('I need params id_author, name_author', 404);
        }

        public function deleteAuthors()
        {
            list($id) = explode('/', $this->params);
            $idAuthor = Validator::checkId($id)?$id:false;

            if ($idAuthor)
            {
                $sth = $this->pdo->prepare("DELETE FROM authors WHERE id = '$idAuthor'");
                $sth->execute();

                if ($sth->rowCount() > 0)
                    $this->createResponse('Success DELETE author', 202);
                else
                    $this->createResponse('We didn\'t find this author', 404);
            }
            else
                $this->createResponse('I need id of author', 404);
        }

        public function getAuthorsByParams()
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
                $sth = $this->pdo->query("SELECT id, name FROM authors WHERE $where");

                if ($sth)
                {
                    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

                    if (count($data)>0)
                        $this->createResponse($data, 200);
                    else
                        $this->createResponse('Empty', 200);
                }
                else
                    $this->createResponse('Incorrect request', 404);
            }
            else
                $this->getAuthors();
        }
    }

$obj = new Authors();
$obj->start();

