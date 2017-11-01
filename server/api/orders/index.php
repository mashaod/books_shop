<?php
include ('../../utils/Rest.php');

Class Orders extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getOrders()
        {
            $sth = $this->pdo->query(
                "SELECT o.id, o.id_user, o.id_status, o.date, o.total_order_price, s.name as status_order,
                u.login, du.discount as user_discount, p.name as pay_name,
                GROUP_CONCAT(b.name SEPARATOR ', ') as books,
                GROUP_CONCAT(db.discount  SEPARATOR ', ') as books_discounts,
                GROUP_CONCAT(b.price SEPARATOR ', ') as prices,
                GROUP_CONCAT(oi.count SEPARATOR ', ') as count
                FROM orders AS o
                LEFT JOIN statuses AS s ON s.id=o.id_status 
                LEFT JOIN users AS u ON u.id=o.id_user
                LEFT JOIN discounts AS du ON du.id=u.id_discount
                LEFT JOIN payments AS p ON p.id=o.id_payment
                LEFT JOIN orders_info as oi ON oi.id_order=o.id
                LEFT JOIN books as b ON b.id=oi.id_book
                LEFT JOIN discounts AS db ON db.id=b.id_discount
                GROUP BY o.id   
                ");

            if ($sth)
            {
                $result = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($result, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postOrders()
        {
            if (Validator::checkParams($this->params, array('id_user', 'data_books', 'id_payment', 'total_order_price')))
            {
                $idUser = $this->params['id_user'];
                $dataBooks = Converter::convertPut($this->params['data_books']);
                $idPayment = $this->params['id_payment'];
                $totalOrderPrice = $this->params['total_order_price'];

                if ($dataBooks)
                {
                    $sth = $this->pdo->prepare("INSERT INTO orders
                        (id_user, id_payment, total_order_price)
                        VALUES ('$idUser', '$idPayment', '$totalOrderPrice')");

                    if ($sth->execute())
                    {
                        $idOrder = $this->pdo->lastInsertId();

                        foreach ($dataBooks as $book=>$count)
                        {
                            if (is_numeric($book) && is_numeric($count))
                            {
                                $sth = $this->pdo->prepare("INSERT INTO orders_info
                                (id_order, id_book, count)
                                VALUES ('$idOrder', '$book', '$count')");
                                $sth->execute();
                            }
                        }               

                        $this->createResponse('Success POST', 201);             
                    }
                    else
                        $this->createResponse('Inncorrect request', 404);
                }
                else
                    $this->createResponse('I need correct params data_books', 404);  
            }
            else
                $this->createResponse('I need id_user, data_books, id_payment, total_order_price', 404); 
        }


        public function putOrders()
        {
            if (Validator::checkParams($this->params, array('id', 'name', 'description', 'price', 'id_discount', 'images', 'id_authors', 'id_genres')))
            {
                $id_book = $this->params['id'];
                $name = $this->params['name'];
                $description = $this->params['description'];
                $price = $this->params['price'];
                $idDiscount = $this->params['id_discount'];
                $images = $this->params['images'];
                $idAuthors = Validator::clearData(explode('%2C', $this->params['id_authors']));
                $idGenres = Validator::clearData(explode('%2C', $this->params['id_genres']));                        
                
                $query = "UPDATE books 
                    SET name = '$name',
                        description = '$description',
                        price = '$price',
                        id_discount = '$idDiscount',
                        images = '$images'
                    WHERE `id` = '$id_book'";

                $sth = $this->pdo->prepare($query);
                $sth->execute();

                if ($sth->rowCount() > 0)
                {
                    /*Because of a discrepancy in the quantity of authors or genres use DELETE with UPDATE*/
                    $query = "DELETE ba, bg FROM book_to_author AS ba INNER JOIN book_to_genre AS bg
                        WHERE ba.id_book=bg.id_book and ba.id_book = '$id_book'";

                    $sth = $this->pdo->prepare($query);
                    if ($sth->execute())
                    {
                        foreach ($idAuthors as $idAuthor)
                        {    
                            $sth = $this->pdo->prepare("INSERT INTO book_to_author
                                (id_book, id_author)
                                VALUES ('$id_book', '$idAuthor')");

                            if (!$sth->execute())
                                $this->createResponse('Invalid operetion add new authors', 404);
                        }

                        foreach ($idGenres as $idGenre)
                        {    
                            $sth = $this->pdo->prepare("INSERT INTO book_to_genre
                                (id_book, id_genre)
                                VALUES ('$id_book', '$idGenre')");

                             if (!$sth->execute())
                                $this->createResponse('Invalid operetion add new genres', 404);
                        }
                        
                        $this->createResponse('Success PUT book', 202);
                    }
                    else
                        $this->createResponse('Invalid operetion delete old authors and genres', 404);
                }
                else
                    $this->createResponse('We didn\'t find this book', 404);
            }
            else
                $this->createResponse('I need params name, description, price, id_discount, images, id_book, id_authors, id_genres', 404);
        }

        public function getOrdersByParams()
        {
            list($id) = explode('/', $this->params);
            $idUser = Validator::checkId($id)?$id:false;

            if ($idUser)
            {
                $sth = $this->pdo->query(
                    "SELECT o.id as id_order, o.id_user, o.id_status, o.date, o.total_order_price,
                    u.login, du.discount as user_discount, p.name as payment, 
                    GROUP_CONCAT(b.name SEPARATOR ', ') as books,
                    GROUP_CONCAT(db.discount SEPARATOR ', ') as books_discounts,
                    GROUP_CONCAT(b.price SEPARATOR ', ') as prices,
                    GROUP_CONCAT(oi.count SEPARATOR ', ') as count,
                    st.name as status
                    FROM orders AS o
                    LEFT JOIN users AS u ON u.id=o.id_user
                    LEFT JOIN discounts AS du ON du.id=u.id_discount
                    LEFT JOIN payments AS p ON p.id=o.id_payment
                    LEFT JOIN orders_info as oi ON oi.id_order=o.id
                    LEFT JOIN books as b ON b.id=oi.id_book
                    LEFT JOIN discounts AS db ON db.id=b.id_discount
                    LEFT JOIN statuses AS st ON st.id=o.id_status
                    WHERE o.id_user = '$idUser'
                    GROUP BY o.id   
                    ");

                if ($sth)
                {
                    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

                    if(count($data)>0)
                        $this->createResponse($data, 200);
                    else
                        $this->createResponse(ERR_111, 404);
                }
                else
                    $this->createResponse('Inncorrect request SELECT by params', 404);
            }
            else
                $this->createResponse('Incoorect params, I need Id of user', 404);
        }
    }

$obj = new Orders();
$obj->start();

