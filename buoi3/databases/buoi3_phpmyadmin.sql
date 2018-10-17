-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2018 at 04:46 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `buoi3`
--

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE IF NOT EXISTS `sanpham` (
  `idsp` int(10) unsigned NOT NULL,
  `tensanpham` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `chitietsp` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `giasp` int(10) unsigned NOT NULL,
  `hinhanhsp` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `idtv` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idsp`),
  KEY `fk_sanpham_thanhvien` (`idtv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thanhvien`
--

CREATE TABLE IF NOT EXISTS `thanhvien` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tendangnhap` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `matkhau` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `hinhanh` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `gioitinh` int(11) NOT NULL DEFAULT '1',
  `nghenghiep` int(11) NOT NULL,
  `sothich` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `thanhvien`
--

INSERT INTO `thanhvien` (`id`, `tendangnhap`, `matkhau`, `hinhanh`, `gioitinh`, `nghenghiep`, `sothich`) VALUES
(1, 'trinhvanlinh', '91f43538d70083e241b5d42099f664da', 'trinhvanlinh.jpeg', 1, 1, 'Thá»i trang'),
(2, 'noname', '1dbb720206c6d9fb3cc2428465f41ed8', 'noname.png', 1, 2, 'Thá»ƒ thao'),
(3, 'Nguy?n Th? Thúy H?ng', '123', '123', 1, 1, ''),
(4, 'Nguy?n Th? Thúy H?ng', '123', '', 1, 1, NULL),
(5, 'Nguyễn Thị Thúy Hằng', '', '', 1, 1, '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `fk_sanpham_thanhvien` FOREIGN KEY (`idtv`) REFERENCES `thanhvien` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
