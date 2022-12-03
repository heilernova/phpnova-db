# Phpnova | Database
Paquete para el manejo de consulta SQL en MySQL y PostgreSQL
# Intalación
Ejecutar el siguiente comando en composer
```
composer require phpnova/db
```
# Ejecutar una consulta SQL
```php
require __DIR__ . '/../vendor/autoload.php';

use Phpnova\Database\db;

# Creamos la conexión con la base de datos
$client = db::connect()->mysql('root', 'password', 'database_name');

$result = $client->query("SELECT * FROM `table`");

# Impirmimos el resultado en formato JSON
header('content-type: application/json');
echo json_encode($result);
```