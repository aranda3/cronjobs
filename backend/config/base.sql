CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255),
  stripe_customer_id VARCHAR(100),
  rol VARCHAR(100) NOT NULL,
  tienda_id INT,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tiendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL,
  propietario_id INT NOT NULL,
  estado VARCHAR(100) NOT NULL,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (propietario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE suscripciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  propietario_id INT NOT NULL,
  stripe_subscription_id VARCHAR(100) NOT NULL,
  stripe_price_id VARCHAR(100) NOT NULL,
  estado ENUM('activa', 'inactiva') DEFAULT 'activa',
  creada_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  nivel_plan INT NOT NULL,
  FOREIGN KEY (propietario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tienda_id INT NOT NULL,                       -- Clave foránea a tiendas
    codigo VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(50),
    categoria VARCHAR(50),
    precio_venta DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    precio_compra DECIMAL(10,2) DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    unidad VARCHAR(20) DEFAULT 'unidad',
    paquete INT DEFAULT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (tienda_id, codigo),                   -- SKU único por tienda
    INDEX idx_nombre (nombre),
    INDEX idx_categoria (categoria),
    INDEX idx_tienda (tienda_id),
    CONSTRAINT fk_producto_tienda FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tienda_id INT NOT NULL, 
    usuario_id INT NOT NULL,                       
    total DECIMAL(10,2) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id, tienda_id, usuario_id), 
    CONSTRAINT fk_venta_tienda FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_venta_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,   
    venta_id INT NOT NULL, 
    precio_venta DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL,   
    subtotal DECIMAL(10,2) NOT NULL,
    UNIQUE (producto_id, venta_id),
    CONSTRAINT fk_detventa_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detventa_venta FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO usuarios (email, password_hash, stripe_customer_id) VALUES
('morin@gmail.com', '123', "cus_SYqjMQqB9vX7sp");

INSERT INTO productos (
  tienda_id, codigo, nombre, marca, categoria,
  precio, costo, stock, unidad, activo
) VALUES
(2, 'ARZ1001', 'Arroz Costeño 1kg', 'Costeño', 'Granos', 18.00, 12.50, 120, 'kg', TRUE),
(2, 'ACE9002', 'Aceite Vegetal 900ml', 'Primor', 'Aceites', 29.50, 20.00, 80, 'ml', TRUE),
(2, 'LGL1003', 'Leche Gloria Lata', 'Gloria', 'Lácteos', 4.00, 2.80, 200, 'lata', TRUE),
(2, 'DTG1004', 'Detergente Bolívar 1kg', 'Bolívar', 'Limpieza', 7.90, 5.10, 50, 'kg', TRUE),
(2, 'PHG1005', 'Papel Higiénico x4', 'Elite', 'Hogar', 12.00, 7.50, 35, 'paquete', FALSE);

/*CREATE TABLE empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tienda_id INT NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  nombre VARCHAR(100),
  rol ENUM('admin', 'vendedor') NOT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE
);

INSERT INTO planes (nombre, stripe_price_id, precio, max_admins, max_vendedores) VALUES
('Plan Básico', 'price_1RdeE9QoRt3Gj9iGdkFSSKCd', 1999, 1, 2),
('Plan Medio', 'price_1RdeF4QoRt3Gj9iG4Jzyx26Q', 2999, 2, 4),
('Plan Avanzado', 'price_1RdeG0QoRt3Gj9iG0jf3mPkw', 3999, 3, 10);

CREATE TABLE planes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50),
  stripe_price_id VARCHAR(100),
  precio INT,
  max_admins INT,
  max_vendedores INT
);*/

