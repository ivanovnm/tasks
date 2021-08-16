-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Янв 29 2021 г., 17:09
-- Версия сервера: 5.5.25
-- Версия PHP: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `realty`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ao_admins`
--

CREATE TABLE IF NOT EXISTS `ao_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `sid` varchar(32) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `ao_admins`
--

INSERT INTO `ao_admins` (`id`, `login`, `password`, `name`, `email`, `sid`, `active`) VALUES
(1, '21232f297a57a5a743894a0e4a801fc3', 'bff29fe2c3269812851d6fda69b3472d', 'Администратор', 'kiesoft@yandex.ru', 'cce85b9ac8b908ff7ec5ec1377014a4d', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `ao_site_settings`
--

CREATE TABLE IF NOT EXISTS `ao_site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fio` varchar(256) DEFAULT NULL,
  `site_name` varchar(256) DEFAULT NULL,
  `rekvizit` varchar(2048) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_pwd` varchar(128) NOT NULL,
  `tel` varchar(128) DEFAULT NULL,
  `skype` varchar(256) DEFAULT NULL,
  `addr_u` varchar(512) DEFAULT NULL,
  `addr_p` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `ao_site_settings`
--

INSERT INTO `ao_site_settings` (`id`, `fio`, `site_name`, `rekvizit`, `email`, `email_pwd`, `tel`, `skype`, `addr_u`, `addr_p`) VALUES
(1, 'Администратор{space}системы', '', 'ИНН{lslash}КПП{space}Сч.{space}№', 'senya.vorontsov2014@yandex.ru', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ao_site_struct`
--

CREATE TABLE IF NOT EXISTS `ao_site_struct` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '1',
  `url` varchar(1024) DEFAULT NULL,
  `system` int(11) NOT NULL DEFAULT '0',
  `params` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '999999',
  `template_id` int(11) NOT NULL DEFAULT '1',
  `tag_title` varchar(1024) DEFAULT NULL,
  `tag_keywords` varchar(2048) DEFAULT NULL,
  `tag_description` varchar(2048) DEFAULT NULL,
  `menu_name` varchar(32) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=135 ;

--
-- Дамп данных таблицы `ao_site_struct`
--

INSERT INTO `ao_site_struct` (`id`, `parent_id`, `url`, `system`, `params`, `order`, `template_id`, `tag_title`, `tag_keywords`, `tag_description`, `menu_name`, `name`, `description`) VALUES
(1, 0, '', 0, 0, 1, 15, '', '', '', 'Главная', 'Главная{s}страница', 'Описание{s}страницы{s}для{s}себя,{s}что{s}бы{s}понимать,{s}что{s}эта{s}за{s}страница.{s}могут{s}размещаться{s}разного{s}рода{s}комментарии,{s}что{s}бы{s}не{s}запутаться.'),
(2, 1, 'admin', 1, 0, 1, 2, 'Администрирование', NULL, NULL, 'Авторизация', 'Авторизация пользователей', 'Форма авторизации администраторов сайта'),
(3, 2, 'auth', 1, 0, 1, 2, 'Авторизация', 'Авторизация', 'Авторизация', 'Авторизация', 'Авторизация администраторов', 'Форма авторизации администраторов для доступа в раздел управления сайтом'),
(4, 2, 'sitestruct', 1, 0, 2, 25, 'Управление структурой сайта', NULL, NULL, 'Управление структурой', 'Управление структурой сайта', 'Управление структурой и содержимым сайта'),
(5, 2, 'contents', 1, 0, 3, 25, '', '', '', 'Содержимое{s}страниц', '', 'Описание{s}раздела{s}заполнения{s}страниц{s}контентом'),
(6, 2, 'templates', 1, 0, 4, 25, 'Шаблоны', '', '', 'Шаблоны', 'Шаблоны', ''),
(9, 2, 'logoff', 1, 0, 8, 2, '', '', '', 'Выход', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ao_site_struct_blocks`
--

CREATE TABLE IF NOT EXISTS `ao_site_struct_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_struct_id` int(11) DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `content` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=982 ;

--
-- Дамп данных таблицы `ao_site_struct_blocks`
--

INSERT INTO `ao_site_struct_blocks` (`id`, `site_struct_id`, `name`, `type`, `content`) VALUES
(5, 2, 'content', 'module', 'adminauth=menu'),
(6, 2, 'content', 'module', 'adminauth'),
(7, 3, 'content', 'module', 'adminauth=auth'),
(8, 4, 'content', 'module', 'adminauth=menu'),
(9, 4, 'content', 'module', 'sitestructmgr'),
(10, 5, 'content', 'module', 'adminauth=menu'),
(11, 5, 'content', 'module', 'contentmgr'),
(19, 6, 'content', 'module', 'adminauth{ravno}menu'),
(20, 6, 'content', 'module', 'templatemgr'),
(21, 7, 'content', 'module', 'adminauth{ravno}menu'),
(22, 7, 'content', 'module', 'modulemgr'),
(23, 8, 'content', 'module', 'adminauth{ravno}menu'),
(24, 8, 'content', 'module', 'settingsmgr'),
(25, 9, 'content', 'module', 'adminauth{ravno}logoff'),
(44, 16, 'content', 'module', 'adminauth{ravno}menu'),
(45, 16, 'content', 'module', 'publicsmgr'),
(46, 17, 'content', 'module', 'adminauth{ravno}menu'),
(47, 17, 'content', 'module', 'adsmgr'),
(241, 51, 'left', 'helper', 'menu'),
(945, 1, 'content', 'text', '1-2-3-4'),
(947, 1, 'title', 'text', 'Главная{s}страница{s}сайта'),
(949, 115, 'content', 'module', 'adminauth{e}menu'),
(962, 128, 'headeText', 'text', 'Реестр{s}компаний'),
(963, 129, 'headeText', 'text', 'Лот'),
(964, 129, 'content', 'module', 'lots{e}lotAction'),
(965, 130, 'content', 'module', 'managers'),
(966, 130, 'userauth', 'module', 'managers{e}userAuth'),
(967, 132, 'content', 'module', 'managers{e}authForm'),
(969, 132, 'userauth', 'text', '');

-- --------------------------------------------------------

--
-- Структура таблицы `ao_site_struct_params`
--

CREATE TABLE IF NOT EXISTS `ao_site_struct_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `site_struct_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=195 ;

--
-- Дамп данных таблицы `ao_site_struct_params`
--

INSERT INTO `ao_site_struct_params` (`id`, `name`, `site_struct_id`, `order`) VALUES
(33, 'action', 15, 0),
(64, 'action', 14, 0),
(37, 'publication', 19, 1),
(36, 'section', 19, 0),
(38, 'section', 20, 0),
(39, 'publication', 20, 1),
(43, 'publication', 21, 1),
(42, 'section', 21, 0),
(44, 'section', 22, 0),
(45, 'publication', 22, 1),
(46, 'section', 23, 0),
(47, 'publication', 23, 1),
(48, 'section', 24, 0),
(49, 'publication', 24, 1),
(50, 'section', 25, 0),
(51, 'publication', 25, 1),
(52, 'section', 26, 0),
(53, 'publication', 26, 1),
(54, 'section', 27, 0),
(55, 'publication', 27, 1),
(56, 'section', 28, 0),
(57, 'publication', 28, 1),
(63, 'publication', 30, 1),
(62, 'section', 30, 0),
(60, 'section', 18, 0),
(61, 'publication', 18, 1),
(68, 'publication', 37, 1),
(67, 'section', 37, 0),
(69, 'section', 38, 0),
(70, 'publication', 38, 1),
(71, 'section', 39, 0),
(72, 'publication', 39, 1),
(76, 'publication', 40, 1),
(75, 'section', 40, 0),
(77, 'section', 41, 0),
(78, 'publication', 41, 1),
(79, 'section', 42, 0),
(80, 'publication', 42, 1),
(81, 'section', 43, 0),
(82, 'publication', 43, 1),
(83, 'section', 44, 0),
(84, 'publication', 44, 1),
(85, 'section', 45, 0),
(86, 'publication', 45, 1),
(87, 'section', 46, 0),
(88, 'publication', 46, 1),
(194, 'publication', 1, 1),
(193, 'section', 1, 0),
(93, 'section', 47, 0),
(94, 'publication', 47, 1),
(124, 'publication', 52, 1),
(123, 'section', 52, 0),
(136, 'publication', 53, 1),
(135, 'section', 53, 0),
(138, 'publication', 54, 1),
(137, 'section', 54, 0),
(142, 'publication', 55, 1),
(141, 'section', 55, 0),
(132, 'publication', 56, 1),
(131, 'section', 56, 0),
(113, 'section', 57, 0),
(114, 'publication', 57, 1),
(139, 'section', 59, 0),
(147, 'section', 60, 0),
(119, 'section', 58, 0),
(120, 'publication', 58, 1),
(148, 'publication', 60, 1),
(140, 'publication', 59, 1),
(144, 'publication', 61, 1),
(143, 'section', 61, 0),
(133, 'action', 63, 0),
(134, 'file', 63, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `ao_site_templates`
--

CREATE TABLE IF NOT EXISTS `ao_site_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(32) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Дамп данных таблицы `ao_site_templates`
--

INSERT INTO `ao_site_templates` (`id`, `menu_name`, `name`, `description`) VALUES
(2, 'Администрирование', 'Админка сайта', 'Шаблон раздела администрирования сайта'),
(15, 'Главная', 'Главная', 'Главная'),
(25, 'Админка с Меню', 'Админка с Меню', 'Админка с Меню'),
(29, 'Внутренняя', 'Внутренняя', ''),
(31, 'Авторизация', 'Авторизация', 'Авторизация');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
