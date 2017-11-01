<?php
include ('../../utils/Rest.php');

Class Users extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getUsers()
        {
            $sth = $this->pdo->query("SELECT u.id, u.login, u.time, u.status, u.rank, d.discount  
            FROM users AS u
            LEFT JOIN discounts AS d ON u.id_discount = d.id");

            if($sth)
            {
                $result = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($result, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }        

        public function postUsers()
        {
            $login =  Validator::checkLogin($this->params['login'])? $this->params['login'] : false;
            $password = Validator::checkPassword($this->params['password'])? password_hash($this->params['password'], PASSWORD_BCRYPT):false;

            if ($login || $password)
            {
                $query = "SELECT id from `users` where login = '$login'";
                $sth = $this->pdo->query($query);

                if (!$sth->fetchColumn()>0)
                {
                    $hash =  md5(mt_rand());

                    $query = "INSERT INTO users (login, password, hash) VALUES ('$login', '$password', '$hash')";
                    $sth = $this->pdo->prepare($query);

                    if ($sth->execute())
                        $this->createResponse('Success add user', 201);
                    else
                        $this->createResponse('Incorrect add user', 404);               
                }
                else
                    $this->createResponse('Reserved login', 404);
            }
            else
                $this->createResponse('I need params login and password');
        }

        public function putUsers()
        {
            if ($this->params['id'])
            {
                foreach ($this->params as $key => $val)
                {
                    switch ($key)
                    {
                        case 'login':
                        Validator::checkLogin($val)?$conditionArray[] = $key . ' = \'' . $val . '\'':'';
                        break;
                        case 'status':
                        (int) $val < 3?$conditionArray[] = $key . ' = \'' . $val . '\'':'';
                        break;
                        case 'rank':
                        ($val == 'user' || $val == 'moderator')?$conditionArray[] = $key . ' = \'' . $val . '\'':'';
                        break;
                        case 'id_discount':
                        $conditionArray[] = (int) $val?$key . ' = \'' . $val . '\'':$key . ' = NULL';
                        break;
                        default:
                        break;
                    }
                }

                if (count($conditionArray)>0)
                {
                    $idUser = $this->params['id'];
                    $condition = join(', ', $conditionArray);
                    $sth = $this->pdo->prepare("UPDATE users SET $condition WHERE id='$idUser'");
    
                    if ($sth->execute())
                        $this->createResponse('Success PUT', 202);
                    else
                        $this->createResponse('Incorrect request', 404);
                }
                else 
                    $this->createResponse('I need params login or status or rank', 404);
            }         
            else
                $this->createResponse('I need id of user', 404);
        }

        public function getUsersByParams()
        {
            list($id) = explode('/', $this->params);
            $idUser = Validator::checkId($id)?$id:false;

            if ($idUser)
            {
                $sth = $this->pdo->query("SELECT u.id, u.login, u.time, u.status, u.rank, d.discount  
                FROM users AS u
                LEFT JOIN discounts AS d ON u.id_discount = d.id
                WHERE u.id = '$idUser'");

                if($sth)
                {
                    $result = $sth->fetch(PDO::FETCH_ASSOC);
                    $this->createResponse($result, 200);
                }
                else
                    $this->createResponse(ERR_999, 404);
            }
            else
                $this->createResponse(ERR_209, 404);               
        }
    }

$obj = new Users();
$obj->start();
