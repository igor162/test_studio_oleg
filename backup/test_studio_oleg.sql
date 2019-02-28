-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 28 2019 г., 14:20
-- Версия сервера: 5.5.60-0ubuntu0.14.04.1
-- Версия PHP: 7.2.7-1+ubuntu14.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test_studio_oleg`
--
CREATE DATABASE IF NOT EXISTS `test_studio_oleg` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `test_studio_oleg`;

-- --------------------------------------------------------

--
-- Структура таблицы `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT 'Название отдела',
  `created_at` int(11) DEFAULT NULL COMMENT 'Создано',
  `created_by` int(11) DEFAULT NULL COMMENT 'Создал',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Обновил',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Обновлено',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_departments_1_idx` (`created_by`),
  KEY `fk_departments_2_idx` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Отделы' AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(4, 'Отдел Снабжения', NULL, NULL, NULL, NULL),
(11, 'Отдел IT', NULL, NULL, NULL, NULL),
(12, 'АХО Отдел ', NULL, NULL, NULL, NULL),
(15, 'Метал Цех', NULL, NULL, NULL, NULL),
(16, 'Складской Учет', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `dep_staff`
--

CREATE TABLE IF NOT EXISTS `dep_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL COMMENT 'Сотрудник',
  `dep_id` int(11) DEFAULT NULL COMMENT 'Должность',
  `created_at` int(11) DEFAULT NULL COMMENT 'Создано',
  `created_by` int(11) DEFAULT NULL COMMENT 'Создал',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Обновил',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Обновлено',
  PRIMARY KEY (`id`),
  KEY `fk_dep_staff_1_idx` (`staff_id`),
  KEY `fk_dep_staff_2_idx` (`dep_id`),
  KEY `fk_dep_staff_3_idx` (`created_by`),
  KEY `fk_dep_staff_4_idx` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Карточка должностей сотрудника' AUTO_INCREMENT=28 ;

--
-- Дамп данных таблицы `dep_staff`
--

INSERT INTO `dep_staff` (`id`, `staff_id`, `dep_id`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(10, 12, 4, 1551254738, 1, NULL, NULL),
(11, 12, 12, 1551254738, 1, NULL, NULL),
(12, 9, 4, 1551254775, 1, NULL, NULL),
(13, 9, 12, 1551254775, 1, NULL, NULL),
(19, 21, 11, 1551255735, 1, NULL, NULL),
(21, 23, 4, 1551256252, 1, NULL, NULL),
(24, 13, 11, 1551267919, 1, NULL, NULL),
(25, 11, 11, 1551335858, 1, NULL, NULL),
(26, 24, 16, 1551351067, 1, NULL, NULL),
(27, 22, 15, 1551351086, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1551082775);

-- --------------------------------------------------------

--
-- Структура таблицы `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) DEFAULT NULL COMMENT 'Имя',
  `last_name` varchar(45) DEFAULT NULL COMMENT 'Фамиля',
  `patronymic` varchar(45) DEFAULT NULL COMMENT 'Отчество',
  `gender` enum('Male','Female') DEFAULT NULL COMMENT 'Пол',
  `wage` int(11) DEFAULT NULL COMMENT 'Заработная плата',
  `created_at` int(11) DEFAULT NULL COMMENT 'Создано',
  `created_by` int(11) DEFAULT NULL COMMENT 'Создал',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Обновил',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Обновлено',
  PRIMARY KEY (`id`),
  KEY `fk_staff_1_idx` (`created_by`),
  KEY `fk_staff_2_idx` (`updated_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Сотрудники' AUTO_INCREMENT=25 ;

--
-- Дамп данных таблицы `staff`
--

INSERT INTO `staff` (`id`, `first_name`, `last_name`, `patronymic`, `gender`, `wage`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(9, 'Петр', 'Сидоров', 'Сидрец', 'Male', 2222, NULL, NULL, 1551335831, 1),
(11, 'Алексей', 'Никитин', 'Петрович', 'Male', 333, NULL, NULL, 1551335858, 1),
(12, 'Илья', 'Андреев', 'Андреевич', 'Male', 223, NULL, NULL, 1551335878, 1),
(13, 'Анатолий', 'Антольев', 'Иванович', 'Female', 233, NULL, NULL, 1551335905, 1),
(14, 'aa', 'dd', 'ff', 'Male', 23, NULL, NULL, NULL, NULL),
(17, 'asd', 'asd', 'asd', 'Male', 234, NULL, NULL, NULL, NULL),
(21, 'Иван', 'Фаров', 'Васильевич', 'Male', 555, 1551255706, 1, 1551335942, 1),
(22, 'Мария', 'Крахмальная', 'Петровна', 'Female', 33434, 1551255972, 1, 1551351086, 1),
(23, 'Светлана', 'Муренко', 'Ивановна', 'Male', 444, 1551256252, 1, 1551335992, 1),
(24, 'Елена', 'Иванченко', 'Алексеевна', 'Male', 43, 1551256515, 1, 1551351067, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `statement_accrual`
--

CREATE TABLE IF NOT EXISTS `statement_accrual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(45) DEFAULT NULL COMMENT 'Сотрудник',
  `month_id` varchar(45) DEFAULT NULL COMMENT 'Месяц',
  `sum` varchar(45) DEFAULT NULL COMMENT 'Сумма начисления',
  `created_at` int(11) DEFAULT NULL COMMENT 'Создано',
  `created_by` int(11) DEFAULT NULL COMMENT 'Создал',
  `updated_at` int(11) DEFAULT NULL COMMENT 'Обновил',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Обновлено',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ведомость начисления' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Логин',
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Ключ авторизации',
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Хэш пароля',
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Токен восстановления',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'E-mail пользователя',
  `status` enum('Actual','Blocked','Deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Actual' COMMENT 'Статус "Пользователя" в системе',
  `created_at` int(11) NOT NULL COMMENT 'Создано',
  `updated_at` int(11) NOT NULL COMMENT 'Обновлено',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'xw6XkIK9Rq6mbDdfuB9NRrkboJgcEQt0', '$2y$13$69CbnnCZbhFTFrgB6V8TqummASMfnnOuWJPxInQuA/Ox2dX.evQje', NULL, '', 'Actual', 0, 0);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_departments_2` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `dep_staff`
--
ALTER TABLE `dep_staff`
  ADD CONSTRAINT `fk_dep_staff_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dep_staff_2` FOREIGN KEY (`dep_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dep_staff_3` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_dep_staff_4` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_staff_2` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
