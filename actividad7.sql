-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Dec 11, 2025 at 04:55 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `actividad7`
--

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `cedula` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `clave` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `pregunta_1_id` int DEFAULT NULL,
  `respuesta_1` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pregunta_2_id` int DEFAULT NULL,
  `respuesta_2` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pregunta_3_id` int DEFAULT NULL,
  `respuesta_3` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('pendiente_activacion','activo') COLLATE utf8mb4_general_ci DEFAULT 'pendiente_activacion',
  `token_otp` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `nombre`, `apellido`, `correo`, `telefono`, `clave`, `pregunta_1_id`, `respuesta_1`, `pregunta_2_id`, `respuesta_2`, `pregunta_3_id`, `respuesta_3`, `estado`, `token_otp`, `token_expiracion`, `created_at`) VALUES
(1, '1756827711', 'Carlos', 'Parreño', 'carlos@pucesa.edu.ec', '0999720694', '25d55ad283aa400af464c76d713c07ad', 1, '4432170f33a27181720d70ee7d6d2dee', 6, '4432170f33a27181720d70ee7d6d2dee', 14, '4432170f33a27181720d70ee7d6d2dee', 'activo', NULL, NULL, '2025-12-11 16:38:54'),
(2, '1805034467', 'Carlos', 'Ortega', 'carlos2@pucesa.edu.ec', '0969099790', '25d55ad283aa400af464c76d713c07ad', 4, '4432170f33a27181720d70ee7d6d2dee', 6, '4432170f33a27181720d70ee7d6d2dee', 13, '4432170f33a27181720d70ee7d6d2dee', 'activo', NULL, NULL, '2025-12-11 16:41:40'),
(3, '1802572915', 'Marcelo', 'Balseca', 'marcelo@pucesa.edu.ec', '0991231702', '25f9e794323b453885f5181f1b624d0b', 1, '4432170f33a27181720d70ee7d6d2dee', 7, '4432170f33a27181720d70ee7d6d2dee', 13, '4432170f33a27181720d70ee7d6d2dee', 'activo', NULL, NULL, '2025-12-11 16:43:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
