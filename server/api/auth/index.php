<?php
include ('../../utils/Rest.php');

Class Auth extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }      

        public function putAuth()
        {
            if (count($this->params)>0)
            {
                $login =  Validator::checkLogin($this->params['login'])? $this->params['login'] : false;
                $password = Validator::checkPassword($this->params['password'])? $this->params['password']:false;

                $query = "SELECT id as id_user, password from users where login = '$login'";
                $sth = $this->pdo->query($query);
                $user = $sth->fetch(PDO::FETCH_ASSOC);

                if (count($user)>0)
                {
                    if (password_verify($password, $user['password']))
                    {
                        $hash =  md5(mt_rand());
                        $time = time();
                        //$time = date_format(time(), 'Y-m-d H:i:s');

                        $query = "UPDATE users SET hash = '$hash', time = '$time' where login = '$login'";
                        $sth = $this->pdo->prepare($query);

                        if ($sth->execute())
                        {
                            $data['id_user']=$user['id_user'];
                            $data['hash']=$hash;
                            $this->createResponse($data, 202);
                        } 
                        else 
                            $this->createResponse(ERR_208, 404);             
                    }
                    else
                        $this->createResponse(ERR_207, 404);
                }
                else
                    $this->createResponse(ERR_206, 404);
            }         
            else
                $this->createResponse(ERR_205, 404);
        }

        public function getAuthByParams()
        {
            list($id_user, $hash) = explode('/', $this->params, 2);
            $id_user = Validator::checkId($id_user)? $id_user : false;

            if ($id_user && $hash && !empty($hash))
            {
                $sth = $this->pdo->prepare("SELECT hash, login, rank from `users` where id = '$id_user'");
                $sth->execute();

                if ($sth->execute())
                {
                    $hashInput = strlen($hash)==32?$hash:false;
                    $dataUser = $sth->fetch(PDO::FETCH_ASSOC);

                    if ($hashInput == $dataUser['hash'])
                    {
                        unset($dataUser['hash']);
                        $this->createResponse($dataUser, 200);
                    }
                    else
                        $this->createResponse('Incorrect hash', 404);
                }
                else
                    $this->createResponse($sth->errorInfo(), 404); 
            }
            else
                $this->createResponse(ERR_209, 404);               
        }
    }

$obj = new Auth();
$obj->start();
