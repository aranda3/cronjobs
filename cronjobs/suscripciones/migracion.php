<?php
/*
try {
    $dsn = "pgsql:host=dpg-d1ob2n49c44c73fcmc40-a;port=5432;dbname=mitienda03_postgres;user=mitienda03_postgres_user;password=FAamO0g0MwEtsCtHVXozYKzDtbaMuNP4";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = <<<SQL

    CREATE TABLE IF NOT EXISTS usuarios (
      id SERIAL PRIMARY KEY,
      email VARCHAR(100) NOT NULL UNIQUE,
      password_hash VARCHAR(255),
      stripe_customer_id VARCHAR(100),
      rol VARCHAR(100) NOT NULL,
      tienda_id INT,
      creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS tiendas (
      id SERIAL PRIMARY KEY,
      nombre VARCHAR(100) NOT NULL,
      slug VARCHAR(100) NOT NULL,
      propietario_id INT NOT NULL,
      estado VARCHAR(100) NOT NULL,
      fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (propietario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS suscripciones (
      id SERIAL PRIMARY KEY,
      propietario_id INT NOT NULL,
      stripe_subscription_id VARCHAR(100) NOT NULL,
      stripe_price_id VARCHAR(100) NOT NULL,
      estado VARCHAR(20) DEFAULT 'activa' CHECK (estado IN ('activa', 'inactiva')),
      creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      nivel_plan INT NOT NULL,
      FOREIGN KEY (propietario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS productos (
      id SERIAL PRIMARY KEY,
      tienda_id INT NOT NULL,
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
      UNIQUE (tienda_id, codigo),
      FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE INDEX IF NOT EXISTS idx_nombre ON productos(nombre);
    CREATE INDEX IF NOT EXISTS idx_categoria ON productos(categoria);
    CREATE INDEX IF NOT EXISTS idx_tienda ON productos(tienda_id);

    CREATE TABLE IF NOT EXISTS ventas (
      id SERIAL PRIMARY KEY,
      tienda_id INT NOT NULL,
      usuario_id INT NOT NULL,
      total DECIMAL(10,2) NOT NULL,
      fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE (id, tienda_id, usuario_id),
      FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE IF NOT EXISTS detalle_ventas (
      id SERIAL PRIMARY KEY,
      producto_id INT NOT NULL,
      venta_id INT NOT NULL,
      precio_venta DECIMAL(10,2) NOT NULL,
      cantidad INT NOT NULL,
      subtotal DECIMAL(10,2) NOT NULL,
      UNIQUE (producto_id, venta_id),
      FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE,
      FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE ON UPDATE CASCADE
    );

    SQL;

    $pdo->exec($sql);
    echo "Migración completada correctamente.";

} catch (PDOException $e) {
    echo "Error al ejecutar la migración: " . $e->getMessage();
}
*/


// Configuración de conexión PostgreSQL en Render
$host = 'dpg-d1ob2n49c44c73fcmc40-a'; 
$port = 5432;
$db   = 'mitienda03_postgres';
$user = 'mitienda03_postgres_user';
$pass = 'FAamO0g0MwEtsCtHVXozYKzDtbaMuNP4';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar datos en la tabla suscripciones
    $sql = "INSERT INTO suscripciones (
        id,
        propietario_id,
        stripe_subscription_id,
        stripe_price_id,
        estado,
        creada_en,
        nivel_plan
    ) VALUES (
        :id,
        :propietario_id,
        :stripe_subscription_id,
        :stripe_price_id,
        :estado,
        :creada_en,
        :nivel_plan
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => 13,
        ':propietario_id' => 2,
        ':stripe_subscription_id' => 'sub_1RfYdMQoRt3Gj9iGCekHekga',
        ':stripe_price_id' => 'price_1RdeF4QoRt3Gj9iG4Jzyx26Q',
        ':estado' => 'activa',
        ':creada_en' => '2025-06-29 22:44:13',
        ':nivel_plan' => 2
    ]);

    echo "✅ Migración completada correctamente.";

} catch (PDOException $e) {
    echo "❌ Error en la migración: " . $e->getMessage();
}

/*
// Configuración de conexión PostgreSQL en Render
$host = 'dpg-d1ob2n49c44c73fcmc40-a'; 
$port = 5432;
$db   = 'mitienda03_postgres';
$user = 'mitienda03_postgres_user';
$pass = 'FAamO0g0MwEtsCtHVXozYKzDtbaMuNP4';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query de inserción
    $sql = "INSERT INTO usuarios (
        id, email, password_hash, stripe_customer_id, rol, tienda_id, creado_en
    ) VALUES (
        :id, :email, :password_hash, :stripe_customer_id, :rol, :tienda_id, :creado_en
    )";

    $stmt = $pdo->prepare($sql);

    // Datos a insertar
    $usuarios = [
        [
            'id' => 2,
            'email' => 'morin@gmail.com',
            'password_hash' => '$2y$10$Iie8kOulFihuWXYd3GXJduN41S6B0Z4ag0h/ENvixJe...',
            'stripe_customer_id' => 'cus_SYqjMQqB9vX7sp',
            'rol' => 'propietario',
            'tienda_id' => 2,
            'creado_en' => '2025-06-29 20:08:24'
        ],
        [
            'id' => 4,
            'email' => 'juan@gmail.com',
            'password_hash' => '$2y$10$00cqm8KUgZMn2sezjgDvxeQWtGvTXk1p51grZqSOSTj...',
            'stripe_customer_id' => null,
            'rol' => 'vendedor',
            'tienda_id' => 2,
            'creado_en' => '2025-07-06 13:27:39'
        ],
        [
            'id' => 5,
            'email' => 'maria',
            'password_hash' => '$2y$10$/wHOSD2BkAalhhGzbKPv1.ZPhx3YD5Nms.vKuXC1kq0...',
            'stripe_customer_id' => null,
            'rol' => 'colaborador',
            'tienda_id' => 2,
            'creado_en' => '2025-07-06 14:52:20'
        ]
    ];

    // Ejecutar inserciones
    foreach ($usuarios as $user) {
        $stmt->execute([
            ':id' => $user['id'],
            ':email' => $user['email'],
            ':password_hash' => $user['password_hash'],
            ':stripe_customer_id' => $user['stripe_customer_id'],
            ':rol' => $user['rol'],
            ':tienda_id' => $user['tienda_id'],
            ':creado_en' => $user['creado_en']
        ]);
    }

    echo "✅ Usuarios insertados correctamente.";

} catch (PDOException $e) {
    echo "❌ Error en la migración: " . $e->getMessage();
}
*/

