-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 13 2018 г., 10:12
-- Версия сервера: 5.7.21-0ubuntu0.17.10.1
-- Версия PHP: 7.0.18-0ubuntu0.16.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bx_fl`
--

-- --------------------------------------------------------

--
-- Структура таблицы `webes_ic_groups`
--

CREATE TABLE `webes_ic_groups` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `webes_ic_ib_params`
--

CREATE TABLE `webes_ic_ib_params` (
  `id` bigint(20) NOT NULL,
  `ib_id` int(11) NOT NULL DEFAULT '0',
  `ib_section` int(11) NOT NULL DEFAULT '0',
  `params` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `webes_ic_items`
--

CREATE TABLE `webes_ic_items` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `group_id` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `webes_ic_set`
--

CREATE TABLE `webes_ic_set` (
  `id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `contents_setted` varchar(500) NOT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `last_price` float NOT NULL DEFAULT '0',
  `last_change_price` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `webes_ic_groups`
--
ALTER TABLE `webes_ic_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Индексы таблицы `webes_ic_ib_params`
--
ALTER TABLE `webes_ic_ib_params`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ib_id` (`ib_id`);

--
-- Индексы таблицы `webes_ic_items`
--
ALTER TABLE `webes_ic_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `name` (`name`,`group_id`);

--
-- Индексы таблицы `webes_ic_set`
--
ALTER TABLE `webes_ic_set`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `last_change_price` (`last_change_price`),
  ADD KEY `parent_id_2` (`parent_id`,`last_change_price`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
