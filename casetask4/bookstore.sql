-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 31 2025 г., 12:57
-- Версия сервера: 10.4.21-MariaDB
-- Версия PHP: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bookstore`
--

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rental_price_week` decimal(10,2) DEFAULT NULL,
  `rental_price_month` decimal(10,2) DEFAULT NULL,
  `rental_price_quarter` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `year`, `price`, `rental_price_week`, `rental_price_month`, `rental_price_quarter`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', 'A story of wealth, love, and the American Dream in the 1920s.', 'Classic', 1925, '12.99', '2.99', '5.99', '15.99', 10, 'great-gatsby.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(2, 'To Kill a Mockingbird', 'Harper Lee', 'A powerful story of racial injustice and moral growth in the American South.', 'Classic', 1960, '14.50', '3.25', '6.50', '17.50', 8, 'mockingbird.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(3, '1984', 'George Orwell', 'A dystopian novel about totalitarianism and surveillance.', 'Science Fiction', 1949, '10.99', '2.50', '5.00', '12.99', 15, '1984.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(4, 'Pride and Prejudice', 'Jane Austen', 'A romantic novel about the Bennett family and their five unmarried daughters.', 'Romance', 1813, '9.99', '2.25', '4.50', '11.99', 12, 'pride-prejudice.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(5, 'The Hobbit', 'J.R.R. Tolkien', 'A fantasy novel about Bilbo Baggins and his adventure with a group of dwarves.', 'Fantasy', 1937, '13.75', '3.00', '6.25', '16.50', 20, 'hobbit.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(6, 'Dune', 'Frank Herbert', 'A science fiction novel set in a distant future amidst a feudal interstellar society.', 'Science Fiction', 1965, '15.25', '3.50', '7.25', '19.99', 7, 'dune.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(7, 'The Catcher in the Rye', 'J.D. Salinger', 'A story about Holden Caulfield and his experiences in New York City.', 'Literary Fiction', 1951, '11.50', '2.75', '5.50', '14.50', 5, 'catcher-rye.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(8, 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 'The first novel in the Harry Potter series.', 'Fantasy', 1997, '16.99', '3.75', '7.50', '20.99', 25, 'harry-potter.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(9, 'The Da Vinci Code', 'Dan Brown', 'A mystery thriller that explores an alternative religious history.', 'Thriller', 2003, '12.25', '2.99', '5.99', '15.50', 18, 'davinci-code.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42'),
(10, 'The Alchemist', 'Paulo Coelho', 'A philosophical book about a young shepherd\'s journey to find treasure.', 'Philosophical Fiction', 1988, '10.50', '2.50', '5.00', '13.25', 14, 'alchemist.jpg', '2025-07-31 10:36:42', '2025-07-31 10:36:42');

-- --------------------------------------------------------

--
-- Структура таблицы `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `book_id`, `purchase_date`, `price`) VALUES
(1, 2, 1, '2023-02-20 08:30:00', '12.99'),
(2, 2, 3, '2023-02-20 08:30:00', '10.99'),
(3, 3, 5, '2023-03-15 11:45:00', '13.75'),
(4, 3, 7, '2023-03-15 11:45:00', '11.50'),
(5, 4, 2, '2023-04-10 07:15:00', '14.50'),
(6, 4, 9, '2023-04-10 07:15:00', '12.25');

-- --------------------------------------------------------

--
-- Структура таблицы `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rental_type` enum('2weeks','month','3months') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','overdue') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `rentals`
--

INSERT INTO `rentals` (`id`, `user_id`, `book_id`, `rental_type`, `start_date`, `end_date`, `status`) VALUES
(1, 2, 4, 'month', '2023-02-25', '2023-03-25', 'completed'),
(2, 2, 6, '2weeks', '2023-03-01', '2023-03-15', 'completed'),
(3, 3, 8, '3months', '2023-03-20', '2023-06-20', 'active'),
(4, 3, 10, 'month', '2023-04-01', '2023-05-01', 'active'),
(5, 4, 1, '2weeks', '2023-04-05', '2023-04-19', 'overdue'),
(6, 4, 3, 'month', '2023-04-10', '2023-05-10', 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bookstore.com', 'admin', '2023-01-01 07:00:00'),
(2, 'john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', 'user', '2023-02-15 11:30:00'),
(3, 'jane_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jane@example.com', 'user', '2023-03-10 06:15:00'),
(4, 'book_lover', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lover@books.com', 'user', '2023-04-05 13:45:00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Индексы таблицы `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Ограничения внешнего ключа таблицы `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
