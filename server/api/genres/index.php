<?php
include ('../../utils/Rest.php');

Class Genres extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getGenres()
        {
            $sth = $this->pdo->query("SELECT id, name FROM Genres");

            if ($sth)
            {
                $data = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($data, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postGenres()
        {
            if ($this->params['name_genre'])
            {
                $nameGenres= $this->params['name_genre'];
                $sth = $this->pdo->prepare("INSERT INTO genres (name) VALUES ('$nameGenres')");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success POST', 201);
                else
                    $this->createResponse('Invalid request POST', 404);
            }
            else
                $this->createResponse('I need name_genre', 404);  
        }

        public function putGenres()
        {
            if (Validator::checkParams($this->params, array('id_genre', 'name_genre')))
            {
                $idGenre = $this->params['id_genre'];
                $nameGenre = $this->params['name_genre'];               
                
                $sth = $this->pdo->prepare("UPDATE genres SET name = '$nameGenre' WHERE id = '$idGenre'");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success PUT genre', 202);
                else
                    $this->createResponse('Incorrect request PUT genre', 404);
            }
            else
                $this->createResponse('I need params id_genre, name_genrer', 404);
        }

        public function deleteGenres()
        {
            list($id) = explode('/', $this->params);
            $idGenre = Validator::checkId($id)?$id:false;

            if ($idGenre)
            {
                $sth = $this->pdo->prepare("DELETE FROM genres WHERE id = '$idGenre'");
                $sth->execute();
                
                if ($sth->rowCount() > 0)
                    $this->createResponse('Success DELETE genre', 202);
                else
                    $this->createResponse('We didn\'t find this genre', 404);
            }
            else
                $this->createResponse('I need id of genre', 404);
        }

        public function getGenresByParams()
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
                $sth = $this->pdo->query("SELECT id, name FROM genres where $where");

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

$obj = new Genres();
$obj->start();