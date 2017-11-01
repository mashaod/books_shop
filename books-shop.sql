-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 18 2017 г., 12:53
-- Версия сервера: 5.6.37
-- Версия PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `user1`
--
CREATE DATABASE IF NOT EXISTS `user1` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `user1`;

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `authors`
--

INSERT INTO `authors` (`id`, `name`) VALUES
(1, 'Joseph Addison'),
(2, 'Robert Anderson'),
(3, 'George Byron'),
(4, 'Ken Follett'),
(5, 'William Collins'),
(7, 'new-author-name2');

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL,
  `id_discount` int(11) DEFAULT NULL,
  `images` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `price`, `id_discount`, `images`) VALUES
(1, 'The Spectator', 'The Spectator was a daily publication founded by Joseph Addison and Richard Steele in England, lasting from 1711 to 1712. Each paper, or number, was approximately 2,500 words long, and the original run consisted of 555 numbers, beginning on 1 March 1711.', 140, NULL, '1.jpg'),
(2, 'The Silence of God', 'The author gives a thoroughly scriptural answer to the issue of God\'s silence for nearly 2,000 years. Also available in Spanish!', 120, NULL, '2.jpg'),
(3, 'Don Juan', 'Byron\'s exuberant masterpiece tells of the adventures of Don Juan, beginning with his illicit love affair at the age of sixteen in his native Spain and his subsequent exile to Italy. Following a dramatic shipwreck, his exploits take him to Greece, where he is sold as a slave, and to Russia, where he becomes a favourite of the Empress Catherine who sends him on to England.', 160, NULL, '3.jpg'),
(4, 'Manfred', 'Manfred contains supernatural elements, in keeping with the popularity of the ghost story in England at the time. It is a typical example of a Romantic closet drama. Manfred was adapted musically by Robert Schumann in 1852, in a composition entitled Manfred: Dramatic Poem with music in Three Parts, and later by Pyotr Ilyich Tchaikovsky in his Manfred Symphony, Op. 58, as well as by Carl Reinecke.', 130, NULL, '4.jpg'),
(5, 'Cain a Mystery', 'Discover the esoteric significance of this ancient drama. \"Cain can be conceived as the action of compressive force, and Abel as that of expansive force. These two action, issues of the same source, are hostile from the moment of their birth, according to the manner by which everything exists in nature.', 140, NULL, '5.jpg'),
(6, 'A Column of Fire', 'International bestselling author Ken Follett has enthralled millions of readers with The Pillars of the Earth and World Without End, two stories of the Middle Ages set in the fictional city of Kingsbridge. The saga now continues with Follett’s magnificent new epic, A Column of Fire.', 99, NULL, '6.jpg'),
(7, 'World Without End', 'In 1989, Ken Follett astonished the literary world with The Pillars of the Earth, a sweeping epic novel set in twelfth-century England centered on the building of a cathedral and many of the hundreds of lives it affected. Critics were overwhelmed and readers everywhere hoped for a sequel.', 89, NULL, '7.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `book_to_author`
--

CREATE TABLE `book_to_author` (
  `id_book` int(11) NOT NULL,
  `id_author` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `book_to_author`
--

INSERT INTO `book_to_author` (`id_book`, `id_author`) VALUES
(3, 3),
(4, 3),
(5, 3),
(2, 2),
(1, 1),
(1, 2),
(5, 1),
(6, 4),
(6, 5),
(7, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `book_to_genre`
--

CREATE TABLE `book_to_genre` (
  `id_book` int(11) NOT NULL,
  `id_genre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `book_to_genre`
--

INSERT INTO `book_to_genre` (`id_book`, `id_genre`) VALUES
(3, 2),
(7, 2),
(3, 5),
(7, 5),
(1, 1),
(1, 6),
(2, 2),
(2, 5),
(4, 1),
(4, 3),
(5, 2),
(5, 1),
(5, 3),
(6, 5),
(6, 6);

-- --------------------------------------------------------

--
-- Структура таблицы `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `percent` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `discounts`
--

INSERT INTO `discounts` (`id`, `name`, `percent`) VALUES
(1, 'discount_5', 5),
(2, 'discount_10', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Fiction'),
(2, 'Non-fiction'),
(3, 'Autobiography'),
(4, 'Mystery'),
(5, 'Romance'),
(6, 'Satire');

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payments`
--

INSERT INTO `payments` (`id`, `name`) VALUES
(1, 'LiqPay'),
(2, 'PayPal'),
(3, 'WebMoney'),
(4, 'Yandex.Money');

-- --------------------------------------------------------

--
-- Структура таблицы `rest_cars`
--

CREATE TABLE `rest_cars` (
  `id_car` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(4) NOT NULL,
  `engine` int(4) NOT NULL,
  `color` varchar(30) NOT NULL,
  `speed` int(4) NOT NULL,
  `price` int(7) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `rest_cars`
--

INSERT INTO `rest_cars` (`id_car`, `brand`, `model`, `year`, `engine`, `color`, `speed`, `price`) VALUES
(1, 'Bugatti', 'BG-6', 2009, 5702, 'red', 330, 1800000),
(2, 'Bugatti', 'BG-7', 2010, 5801, 'yellow', 300, 1900000),
(3, 'Bugatti', 'BG-8', 2011, 7995, 'black', 380, 2500000),
(4, 'Rolls-Royce', 'ZM10', 2010, 5895, 'white', 240, 1800000),
(5, 'Rolls-Royce', 'ZM11', 2011, 6592, 'red', 250, 170000),
(6, 'Rolls-Royce', 'ZM12', 2012, 6992, 'black', 280, 2300000),
(7, 'Jaguar', 'XF', 2010, 2396, 'black', 290, 900000),
(8, 'Jaguar', 'XM', 2011, 2690, 'red', 275, 130000),
(9, 'Jaguar', 'XL', 2012, 2993, 'white', 280, 1700000),
(10, 'Chevrolet', 'CHE-L', 2009, 2793, 'yellow', 250, 100000),
(11, 'Chevrolet', 'CHE-M', 2011, 2893, 'black', 245, 800000),
(12, 'Chevrolet', 'CHE-H', 2011, 2993, 'red', 295, 1400000),
(15, '123467', '1234', 1234, 1234, '1234', 1324, 1234);

-- --------------------------------------------------------

--
-- Структура таблицы `rest_orders`
--

CREATE TABLE `rest_orders` (
  `id_orders` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_car` int(11) NOT NULL,
  `payment` varchar(50) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'hold'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `rest_orders`
--

INSERT INTO `rest_orders` (`id_orders`, `id_user`, `id_car`, `payment`, `status`) VALUES
(1, 1, 3, 'card', 'done'),
(2, 1, 4, 'cash', 'hold'),
(3, 1, 4, 'card', 'hold'),
(4, 1, 4, 'card', 'delivery'),
(5, 1, 3, 'card', 'delivery'),
(6, 1, 3, 'cash', 'hold'),
(7, 1, 2, 'card', 'hold'),
(8, 1, 3, 'card', 'hold'),
(9, 2, 5, 'card', 'delivery'),
(13, 2, 6, 'cash', 'hold'),
(10, 2, 6, 'card', 'delivery'),
(11, 2, 6, 'cash', 'done'),
(12, 2, 3, 'cash', 'hold');

-- --------------------------------------------------------

--
-- Структура таблицы `rest_users`
--

CREATE TABLE `rest_users` (
  `id_user` int(11) NOT NULL,
  `login` varchar(15) NOT NULL,
  `password` varchar(250) NOT NULL,
  `hash` varchar(60) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `rest_users`
--

INSERT INTO `rest_users` (`id_user`, `login`, `password`, `hash`, `time`) VALUES
(1, 'maria', '$2y$10$pE2K7ou.7zc3/u2AG67WEOSN2JaO7brSKJ1ER8dwdwjpnhG8F5fKS', '3b75309f64b1291bd0880a907a5b6001', 1508095080),
(2, 'yuriy', '$2y$10$ncha91RTQouuqtOxewM9YOpR5D0AlYX4vIYePDc8a.WW7/z105Wge', 'bc6f2d305828f21a8ec53f9fefc0d298', 1508163499);

-- --------------------------------------------------------

--
-- Структура таблицы `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `statuses`
--

INSERT INTO `statuses` (`id`, `name`) VALUES
(1, 'expected'),
(2, 'in progress'),
(3, 'complete');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `books_fk0` (`id_discount`);

--
-- Индексы таблицы `book_to_author`
--
ALTER TABLE `book_to_author`
  ADD KEY `book_to_author_fk0` (`id_book`),
  ADD KEY `book_to_author_fk1` (`id_author`);

--
-- Индексы таблицы `book_to_genre`
--
ALTER TABLE `book_to_genre`
  ADD KEY `book_to_genre_fk0` (`id_book`),
  ADD KEY `book_to_genre_fk1` (`id_genre`);

--
-- Индексы таблицы `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `rest_cars`
--
ALTER TABLE `rest_cars`
  ADD PRIMARY KEY (`id_car`);

--
-- Индексы таблицы `rest_orders`
--
ALTER TABLE `rest_orders`
  ADD PRIMARY KEY (`id_orders`);

--
-- Индексы таблицы `rest_users`
--
ALTER TABLE `rest_users`
  ADD PRIMARY KEY (`id_user`);

--
-- Индексы таблицы `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `rest_cars`
--
ALTER TABLE `rest_cars`
  MODIFY `id_car` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT для таблицы `rest_orders`
--
ALTER TABLE `rest_orders`
  MODIFY `id_orders` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT для таблицы `rest_users`
--
ALTER TABLE `rest_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_fk0` FOREIGN KEY (`id_discount`) REFERENCES `discounts` (`id`);

--
-- Ограничения внешнего ключа таблицы `book_to_author`
--
ALTER TABLE `book_to_author`
  ADD CONSTRAINT `book_to_author_fk0` FOREIGN KEY (`id_book`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `book_to_author_fk1` FOREIGN KEY (`id_author`) REFERENCES `authors` (`id`);

--
-- Ограничения внешнего ключа таблицы `book_to_genre`
--
ALTER TABLE `book_to_genre`
  ADD CONSTRAINT `book_to_genre_fk0` FOREIGN KEY (`id_book`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `book_to_genre_fk1` FOREIGN KEY (`id_genre`) REFERENCES `genres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
