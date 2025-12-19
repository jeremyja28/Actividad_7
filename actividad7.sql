-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Dec 12, 2025 at 02:07 PM
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
-- Table structure for table `atributos`
--

CREATE TABLE `atributos` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `atributos`
--

INSERT INTO `atributos` (`id`, `nombre`) VALUES
(2, 'Capacidad'),
(1, 'Color'),
(3, 'Modelo');

-- --------------------------------------------------------

--
-- Table structure for table `cod_rol`
--

CREATE TABLE `cod_rol` (
  `cod_rol` int NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cod_rol`
--

INSERT INTO `cod_rol` (`cod_rol`, `descripcion`) VALUES
(1, 'Usuario'),
(2, 'Admin'),
(3, 'Administrador'),
(4, 'Vendedor'),
(5, 'Bodeguero'),
(6, 'Cliente');

-- --------------------------------------------------------

--
-- Table structure for table `compras`
--

CREATE TABLE `compras` (
  `id` int NOT NULL,
  `variante_id` int NOT NULL,
  `proveedor_id` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `fecha_compra` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `compras`
--

INSERT INTO `compras` (`id`, `variante_id`, `proveedor_id`, `precio_unitario`, `cantidad`, `fecha_compra`) VALUES
(1, 1, 1, '1000.00', 5, '2025-11-09 02:16:49'),
(2, 2, 2, '950.00', 3, '2025-11-09 02:16:49'),
(3, 1, 2, '1500.00', 5, '2025-11-08 21:18:38'),
(4, 1, 2, '1100.90', 6, '2025-11-09 11:47:31'),
(5, 6, 1, '1200.00', 7, '2025-11-09 11:47:44'),
(6, 5, 2, '1150.20', 11, '2025-11-09 11:48:05'),
(7, 8, 3, '850.00', 5, '2025-11-09 12:03:17'),
(8, 9, 3, '1000.99', 11, '2025-11-09 12:03:33'),
(9, 10, 3, '1200.00', 6, '2025-11-09 12:03:48'),
(10, 5, 2, '1.30', 3, '0002-01-06 08:47:00'),
(11, 11, 2, '1000.00', 10, '2025-11-28 09:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `marcas`
--

CREATE TABLE `marcas` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `marcas`
--

INSERT INTO `marcas` (`id`, `nombre`) VALUES
(1, 'Apple'),
(4, 'Google pixell'),
(2, 'Samsung'),
(3, 'Xiaomi');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `marca_id` int NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `marca_id`, `descripcion`) VALUES
(1, 'iPhone 17', 1, 'Smartphone Apple de última generación'),
(2, 'Galaxy S24', 2, 'Smartphone Samsung gama alta'),
(3, 'Iphone 16', 1, 'Smartphone Apple'),
(4, 'Xiaomi 17', 3, 'Snapdragon 8 Elite Gen 5'),
(5, 'Galaxy S23', 2, 'Smartphone Samsung gama alta'),
(6, 'Iphone 15', 1, 'Smartphone inteligente marca apple'),
(7, 'Google pixel 10', 4, 'Tecnologia de punta con IA');

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombres`, `nombre_empresa`, `direccion`, `ciudad`, `pais`, `email`, `telefono`, `ruta`) VALUES
(1, 'María López', 'Distribuciones Andinas', 'Av. Siempre Viva 123', 'Quito', 'Ecuador', 'maria@andinas.ec', '0934786159', 'img/proveedores/distribuciones_andinas.png'),
(2, 'Juan Pérez', 'Tech Global Supplies', 'Calle 9 #45-10', 'Medellín', 'Colombia', 'juan@techglobal.co', '0945378123', 'img/proveedores/TechGlobal_Supplies.png'),
(3, 'Carlos Parreño', 'Novicompu', 'Calle 9 #45-10', 'Ambato', 'Ecuador', 'pepitotrueno@gmail.com', '0995408705', 'img/proveedores/login_pucesa.png');

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
  `rol_id` int DEFAULT NULL,
  `pregunta_1_id` int DEFAULT NULL,
  `respuesta_1` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pregunta_2_id` int DEFAULT NULL,
  `respuesta_2` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pregunta_3_id` int DEFAULT NULL,
  `respuesta_3` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('pendiente_activacion','activo','inactivo') COLLATE utf8mb4_general_ci DEFAULT 'pendiente_activacion',
  `token_otp` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `nombre`, `apellido`, `correo`, `telefono`, `clave`, `rol_id`, `pregunta_1_id`, `respuesta_1`, `pregunta_2_id`, `respuesta_2`, `pregunta_3_id`, `respuesta_3`, `estado`, `token_otp`, `token_expiracion`, `created_at`) VALUES
(2, '1805034467', 'Carlos', 'Ortega', 'carlos2@pucesa.edu.ec', '0969099790', '25d55ad283aa400af464c76d713c07ad', 2, 4, '4432170f33a27181720d70ee7d6d2dee', 6, '4432170f33a27181720d70ee7d6d2dee', 13, '4432170f33a27181720d70ee7d6d2dee', 'activo', NULL, NULL, '2025-12-11 16:41:40'),
(6, '1804683728', 'Emilio', 'Jimenez', 'emilio@pucesa.edu.ec', '0995324087', '25d55ad283aa400af464c76d713c07ad', 3, 3, '41d1de28e96dc1cde568d3b068fa17bb', 6, '4432170f33a27181720d70ee7d6d2dee', 11, '4432170f33a27181720d70ee7d6d2dee', 'inactivo', NULL, NULL, '2025-12-11 17:28:23'),
(7, '0503821522', 'Carlos', 'Parreño', 'carlos@pucesa.edu.ec', '0999720694', 'e10adc3949ba59abbe56e057f20f883e', 1, 2, 'bb29b938e3fd56a4db433b76857c162b', 7, 'bb29b938e3fd56a4db433b76857c162b', 13, 'bb29b938e3fd56a4db433b76857c162b', 'activo', NULL, NULL, '2025-12-12 13:47:25'),
(8, '1802572915', 'juan', 'perez', 'mail@correo.c', '0991231702', 'e85db05c1778d501e50a71d4731861fb', 2, 1, '552d1156a9b3071181ed713ad028be73', 6, 'c8c7abac0ffc12b6e7076a41da408175', 13, 'd3cd006d4a281dbca88c02da668cefaa', 'activo', NULL, NULL, '2025-12-12 14:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `valores_atributo`
--

CREATE TABLE `valores_atributo` (
  `id` int NOT NULL,
  `atributo_id` int NOT NULL,
  `valor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `valores_atributo`
--

INSERT INTO `valores_atributo` (`id`, `atributo_id`, `valor`) VALUES
(2, 1, 'Azul'),
(16, 1, 'Blanco'),
(20, 1, 'Celeste'),
(13, 1, 'Dorado'),
(17, 1, 'Gris'),
(19, 1, 'Morado'),
(3, 1, 'Negro'),
(15, 1, 'Plateado'),
(1, 1, 'Rojo'),
(14, 1, 'Rosado'),
(18, 1, 'Verde'),
(12, 2, '1 terabyte'),
(4, 2, '128GB'),
(5, 2, '256GB'),
(6, 2, '512GB'),
(7, 3, 'Base'),
(11, 3, 'Plus'),
(8, 3, 'Pro'),
(9, 3, 'Pro Max'),
(10, 3, 'Ultra');

-- --------------------------------------------------------

--
-- Table structure for table `variantes`
--

CREATE TABLE `variantes` (
  `id` int NOT NULL,
  `producto_id` int NOT NULL,
  `sku` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `color_id` int DEFAULT NULL,
  `capacidad_id` int DEFAULT NULL,
  `modelo_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `variantes`
--

INSERT INTO `variantes` (`id`, `producto_id`, `sku`, `precio`, `color_id`, `capacidad_id`, `modelo_id`) VALUES
(1, 1, 'IP17-PM-256-ROJO', '1499.00', 1, 5, 9),
(2, 2, 'GS24-U-512-NEGRO', '1399.00', 3, 6, 10),
(3, 3, 'IP16-PM-512-NEGRO', '1000.03', 1, 6, 9),
(4, 4, 'XI17-PM-512-NEGRO', '1100.50', 3, 6, 7),
(5, 4, 'XI17-PRO-216-ROJO', '1200.99', 1, 5, 8),
(6, 4, 'XI17-PMAX-216-AZUL', '1400.99', 2, 5, 9),
(7, 5, 'GS23-U-512-NEGRO', '950.00', 3, 6, 7),
(8, 6, 'IP15-BAS-128-ROSA', '500.50', 14, 4, 7),
(9, 6, 'IP15-PRO-512-DORADO', '850.00', 13, 6, 8),
(10, 6, 'IP15-PRM-1TER-BLANCO', '1200.50', 16, 12, 9),
(11, 7, 'PX10-PR-512-NEGRO', '1000.00', 14, 6, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atributos`
--
ALTER TABLE `atributos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_atributos_nombre` (`nombre`);

--
-- Indexes for table `cod_rol`
--
ALTER TABLE `cod_rol`
  ADD PRIMARY KEY (`cod_rol`);

--
-- Indexes for table `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compras_variante` (`variante_id`),
  ADD KEY `idx_compras_proveedor` (`proveedor_id`),
  ADD KEY `idx_compras_fecha` (`fecha_compra`);

--
-- Indexes for table `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_marcas_nombre` (`nombre`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_productos_nombre` (`nombre`),
  ADD KEY `idx_productos_marca` (`marca_id`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_proveedores_email` (`email`),
  ADD KEY `idx_proveedores_empresa` (`nombre_empresa`),
  ADD KEY `idx_proveedores_ciudad` (`ciudad`),
  ADD KEY `idx_proveedores_pais` (`pais`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indexes for table `valores_atributo`
--
ALTER TABLE `valores_atributo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_valores_atributo` (`atributo_id`,`valor`),
  ADD KEY `idx_valores_atributo_atributo` (`atributo_id`);

--
-- Indexes for table `variantes`
--
ALTER TABLE `variantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_variantes_sku` (`sku`),
  ADD UNIQUE KEY `uq_variantes_combo` (`producto_id`,`color_id`,`capacidad_id`,`modelo_id`),
  ADD KEY `idx_variantes_producto` (`producto_id`),
  ADD KEY `idx_variantes_color` (`color_id`),
  ADD KEY `idx_variantes_capacidad` (`capacidad_id`),
  ADD KEY `idx_variantes_modelo` (`modelo_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atributos`
--
ALTER TABLE `atributos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cod_rol`
--
ALTER TABLE `cod_rol`
  MODIFY `cod_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `valores_atributo`
--
ALTER TABLE `valores_atributo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `variantes`
--
ALTER TABLE `variantes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_compras_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compras_variante` FOREIGN KEY (`variante_id`) REFERENCES `variantes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_marca` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `valores_atributo`
--
ALTER TABLE `valores_atributo`
  ADD CONSTRAINT `fk_valores_atributo_atributo` FOREIGN KEY (`atributo_id`) REFERENCES `atributos` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `variantes`
--
ALTER TABLE `variantes`
  ADD CONSTRAINT `fk_variantes_capacidad` FOREIGN KEY (`capacidad_id`) REFERENCES `valores_atributo` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variantes_color` FOREIGN KEY (`color_id`) REFERENCES `valores_atributo` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variantes_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `valores_atributo` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variantes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
