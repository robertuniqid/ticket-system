-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 07, 2013 at 08:09 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ticket_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(225) NOT NULL,
  `last_name` varchar(225) NOT NULL,
  `email_address` varchar(225) NOT NULL,
  `phone_number` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `first_name`, `last_name`, `email_address`, `phone_number`) VALUES
(1, 'Andrei-Robert', 'Rusu', 'rusuandreirobert@gmail.com', '0744561062');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_status_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `ticket_category_id` int(11) NOT NULL,
  `title` varchar(225) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_client_idx` (`client_id`),
  KEY `fk_ticket_ticket_category1_idx` (`ticket_category_id`),
  KEY `fk_ticket_ticket_status1_idx` (`ticket_status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`id`, `ticket_status_id`, `client_id`, `ticket_category_id`, `title`, `content`) VALUES
(1, 1, 1, 1, 'I have a problem', '&lt;p&gt;something about my problem&lt;/p&gt;\r\n'),
(2, 1, 1, 1, 'I have a problem', '&lt;p&gt;In depth description&lt;/p&gt;\r\n'),
(3, 1, 1, 1, 'I have a problem', '&lt;p&gt;In depth description&lt;/p&gt;\r\n'),
(4, 1, 1, 1, 'I have a problem', '&lt;p&gt;In depth description&lt;/p&gt;\r\n'),
(5, 1, 1, 1, 'I have a problem', '&lt;p&gt;in depth something&lt;/p&gt;\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_answer`
--

CREATE TABLE `ticket_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_possible_answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_answer_user1_idx` (`user_id`),
  KEY `fk_ticket_answer_ticket_possible_answer1_idx` (`ticket_possible_answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_category`
--

CREATE TABLE `ticket_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ticket_category`
--

INSERT INTO `ticket_category` (`id`, `name`) VALUES
(1, 'Tehnical Problem'),
(2, 'Payment Problem');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_possible_answer`
--

CREATE TABLE `ticket_possible_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_possible_answer_keyword`
--

CREATE TABLE `ticket_possible_answer_keyword` (
  `id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_possible_answer_keyword_has_ticket_possible_answer`
--

CREATE TABLE `ticket_possible_answer_keyword_has_ticket_possible_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_possible_answer_keyword_id` int(11) NOT NULL,
  `ticket_possible_answer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ticket_possible_answer_keyword_has_ticket_possible_answe_idx` (`ticket_possible_answer_id`),
  KEY `fk_ticket_possible_answer_keyword_has_ticket_possible_answe_idx1` (`ticket_possible_answer_keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status`
--

CREATE TABLE `ticket_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ticket_status`
--

INSERT INTO `ticket_status` (`id`, `name`, `type`, `order`) VALUES
(1, 'Received', 0, 1),
(2, 'Completed', 1, 3),
(3, 'Deleted', 3, 4),
(4, 'On-Going', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(225) NOT NULL,
  `last_name` varchar(225) NOT NULL,
  `email_address` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
