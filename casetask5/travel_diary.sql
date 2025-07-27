-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 27 2025 г., 11:22
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
-- База данных: `travel_diary`
--

-- --------------------------------------------------------

--
-- Структура таблицы `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `heritage_sites` text DEFAULT NULL,
  `visit_places` text DEFAULT NULL,
  `comfort_rating` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `trips`
--

INSERT INTO `trips` (`id`, `user_id`, `title`, `location`, `latitude`, `longitude`, `description`, `cost`, `heritage_sites`, `visit_places`, `comfort_rating`, `image_path`, `created_at`) VALUES
(1, 1, 'Отдых в Сочи', 'Сочи, Россия', '43.58547200', '39.72309800', 'Прекрасный отдых на черноморском побережье. Теплое море, живописные горы и отличная инфраструктура.', '45000.00', 'Олимпийский парк, Дендрарий', 'Сочи Парк, Роза Хутор, Красная Поляна', 8, 'images/sochi.jpg', '2025-07-27 12:07:22'),
(2, 1, 'Горный Алтай', 'Республика Алтай, Россия', '51.39722200', '85.67722200', 'Незабываемое путешествие по горному Алтаю. Чистейший воздух, потрясающие пейзажи и гостеприимные местные жители.', '32000.50', 'Плато Укок, Телецкое озеро', 'Чемал, Гейзерное озеро, Кату-Ярык', 9, 'images/altai.jpeg', '2025-07-27 12:07:22'),
(3, 2, 'Парижские каникулы', 'Париж, Франция', '48.85661300', '2.35222200', 'Романтическое путешествие в столицу Франции. Прогулки по Сене, посещение музеев и дегустация французской кухни.', '125000.00', 'Эйфелева башня, Лувр, Нотр-Дам', 'Монмартр, Елисейские поля, Версаль', 7, 'images/paris.jpg', '2025-07-27 12:07:22'),
(4, 2, 'Японские впечатления', 'Токио, Япония', '35.68948700', '139.69170600', 'Погружение в культуру и традиции Японии. Современные технологии соседствуют с древними храмами.', '185000.00', 'Храм Сэнсо-дзи, Императорский дворец', 'Сибуя, Акихабара, Диснейленд', 9, 'images/tokyo.jpg', '2025-07-27 12:07:22'),
(5, 3, 'Зимний Байкал', 'Озеро Байкал, Россия', '53.26963000', '107.77315300', 'Уникальный опыт посещения самого глубокого озера в мире зимой. Прозрачный лед и потрясающие ледяные пещеры.', '58000.75', 'Остров Ольхон, Мыс Хобой', 'Листвянка, Кругобайкальская железная дорога', 6, 'images/baikal.jpg', '2025-07-27 12:07:22'),
(6, 3, 'Золотое кольцо', 'Ярославль, Россия', '57.62656900', '39.89378700', 'Путешествие по древним городам России с богатой историей и архитектурой. Посетили все основные города маршрута.', '35000.00', 'Ярославский кремль, Ростовский кремль', 'Музей \"Музыка и время\", Толгский монастырь', 8, 'images/golden_ring.jpg', '2025-07-27 12:07:22'),
(7, 3, 'Крымские каникулы', 'Ялта, Крым', '44.49519400', '34.16630200', 'Отдых на южном берегу Крыма. Теплое море, горные пейзажи и исторические достопримечательности.', '42000.00', 'Ласточкино гнездо, Воронцовский дворец', 'Никитский ботанический сад, Массандра', 7, 'images/crimea.jpg', '2025-07-27 12:07:22');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'ivanov', 'ivanov@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-07-27 12:06:56'),
(2, 'petrov', 'petrov@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-07-27 12:06:56'),
(3, 'sidorova', 'sidorova@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-07-27 12:06:56');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT для таблицы `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
