<?php
include ('../../utils/Rest.php');

Class Books extends Rest
    {
        protected $pdo;

        public function __construct()
        {
            $this->pdo = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_DB , DB_USER, DB_PASSWORD);
            if (!$this->pdo)
                $this->createResponse(ERR_000, 404);
        }

        public function getBooks()
        {
            $sth = $this->pdo->query(
                "SELECT b.id as id, b.name, b.description, b.id_discount, b.images,
                GROUP_CONCAT(DISTINCT authors.name ORDER BY authors.name ASC SEPARATOR ', ') as authors, 
                GROUP_CONCAT(DISTINCT genres.name ORDER BY genres.name ASC SEPARATOR ', ') as genres,
                b.price,
                d.discount
                FROM books as b 
                left join book_to_author as ba on b.id = ba.id_book
                left join book_to_genre as bg on b.id = bg.id_book 
                left join authors on ba.id_author = authors.id 
                left join genres on bg.id_genre = genres.id
                left join discounts as d on d.id = b.id_discount
                GROUP BY b.id");

            if ($sth)
            {
                $result = $sth->fetchAll(PDO::FETCH_ASSOC);
                $this->createResponse($result, 200);
            }
            else
                $this->createResponse(ERR_999, 404);
        }

        public function postBooks()
        {
            if(Validator::checkParams($this->params, array('name', 'description', 'price', 'id_discount', 'images', 'id_authors', 'id_genres')))
            {
                $name = $this->params['name'];
                $description = $this->params['description'];
                $price = $this->params['price'];
                $idDiscount = $this->params['id_discount'];
                $images = $this->params['images'];
                $idAuthors = Validator::clearData(explode(',', $this->params['id_authors']));
                $idGenres = Validator::clearData(explode(',', $this->params['id_genres']));

                $sth = $this->pdo->prepare("INSERT INTO books
                    (name, description, price, id_discount, images)
                    VALUES ('$name', '$description', '$price', '$idDiscount', '$images')");

                if ($sth->execute())
                   {
                    $idBook = $this->pdo->lastInsertId();

                    foreach ($idAuthors as $idAuthor)
                    {    
                        $sth = $this->pdo->prepare("INSERT INTO book_to_author
                            (id_book, id_author)
                            VALUES ('$idBook', '$idAuthor')");
                        $sth->execute();
                    }

                    foreach ($idGenres as $idGenre)
                    {    
                        $sth = $this->pdo->prepare("INSERT INTO book_to_genre
                            (id_book, id_genre)
                            VALUES ('$idBook', '$idGenre')");
                            $sth->execute();
                    }                

                    $this->createResponse('Success POST', 201);             
                }
                else
                    $this->createResponse('Inncorrect request', 404);
            }
            else
                $this->createResponse($this->params, 200); 
        }


        public function putBooks()
        {
            if (Validator::checkParams($this->params, array('id_book', 'name', 'description', 'price', 'id_discount', 'images', 'id_authors', 'id_genres')))
            {
                $id_book = $this->params['id_book'];
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
                        
                        $this->createResponse($this->params, 202);
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

        public function deleteBooks()
        {
            list($id) = explode('/', $this->params);
            $idBook = Validator::checkId($id)?$id:false;
            
            if ($idBook)
            {
                $sth = $this->pdo->prepare("DELETE ba, bg 
                    FROM book_to_author AS ba 
                    INNER JOIN book_to_genre AS bg
                    WHERE ba.id_book=bg.id_book and ba.id_book = '$idBook'");
                $sth->execute();

                $sth = $this->pdo->prepare("DELETE FROM books WHERE id = '$idBook'");
                $sth->execute();
    
                if ($sth->rowCount() > 0)
                    $this->createResponse(ERR_109, 202);
                else
                    $this->createResponse('Empty', 200);
            }
            else
                $this->createResponse('I need id of book for delete', 404);

        }

        public function getBooksByParams()
        {
            list($params['id'], $params['name'], $params['id_author'], $params['id_genre']) = explode('/', $this->params, 4);
            $params = Validator::clearData($params);

            if (count($params)>0)
            {
                $id_book = $params['id'];
                $name = $params['name'];
                $idAuthor = $params['id_author'];
                $idGenre = $params['id_genre']; 
                $where = '';

                if ($id_book || $name)
                {
                    foreach ($params as $key=>$val)
                    {
                        if ($key == 'name')
                        {
                            $arrayWords = (explode('%20', $val));
                            $string = join(' ', $arrayWords);
                            $operand = ' LIKE ';
                            $val = '%' . $string . '%';
                        }
                        else
                            $operand = '=';

                        $array[] = 'b.'.$key . $operand . '\'' . $val . '\' ';
                    }

                    $string = join('AND ', $array);
                    $where = 'WHERE '.$string;
                }
                elseif ($idGenre && $idAuthor)
                    $where = 'WHERE genres.id = ' . $idGenre . ' and authors.id = ' . $idAuthor;
                elseif ($idGenre)
                    $where = 'WHERE genres.id = ' . $idGenre;
                elseif ($idAuthor)
                    $where = 'WHERE authors.id = ' . $idAuthor;             

                $sth = $this->pdo->query(
                    "SELECT b.id as id, b.name, b.description, b.id_discount, b.images,
                    GROUP_CONCAT(DISTINCT authors.name SEPARATOR ', ') as authors,
                    GROUP_CONCAT(DISTINCT authors.id SEPARATOR ', ') as authors_id, 
                    GROUP_CONCAT(DISTINCT genres.name SEPARATOR ', ') as genres,
                    GROUP_CONCAT(DISTINCT genres.id SEPARATOR ', ') as genres_id,
                    d.discount,
                    b.price
                    FROM books as b 
                    left join book_to_author as ba on b.id = ba.id_book
                    left join book_to_genre as bg on b.id = bg.id_book 
                    left join authors on ba.id_author = authors.id 
                    left join genres on bg.id_genre = genres.id
                    left join discounts as d on d.id = b.id_discount
                    $where 
                    GROUP BY b.id");

                if ($sth)
                {
                    $data = $sth->fetch(PDO::FETCH_ASSOC);

                    if($data && $data !== false)
                        $this->createResponse($data, 200);
                    else
                        $this->createResponse('Empty', 200);
                }
                else
                    $this->createResponse('Inncorrect request SELECT by params', 404);
            }
            else
                $this->getBooks();
        }
    }

$obj = new Books();
$obj->start();

