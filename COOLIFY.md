# Despliegue de Prime Instinct en Coolify

Esta guía te proporcionará instrucciones específicas para desplegar el proyecto Prime Instinct en Coolify ejecutándose en tu PC local.

## Requisitos previos

- Coolify instalado y funcionando en tu PC
- MySQL/MariaDB disponible (puede ser gestionado por Coolify o externo)
- Acceso al panel de administración de Coolify

## Pasos para el despliegue

### 1. Crear una nueva aplicación en Coolify

1. Accede a tu panel de Coolify
2. Haz clic en "New Resource" o "Nuevo Recurso"
3. Selecciona "Application" (Aplicación)
4. Elige "Docker" como el tipo de aplicación

### 2. Configurar el origen del código

Para desplegar desde un directorio local:
1. Selecciona "Local Directory"
2. Navega al directorio donde has copiado el proyecto Prime Instinct

Alternativamente, si has subido el código a un repositorio Git:
1. Selecciona GitHub/GitLab/Gitea según corresponda
2. Conecta tu cuenta si aún no lo has hecho
3. Selecciona el repositorio que contiene el proyecto

### 3. Configurar la compilación

1. La aplicación utilizará el Dockerfile incluido, así que selecciona "Use Dockerfile"
2. **Comando de inicio**: ./coolify-init.sh
3. **Directorio de trabajo**: /var/www/html

### 4. Configurar variables de entorno

Configura las siguientes variables de entorno:

```
DB_HOST=mysql         # O el nombre de tu contenedor de MySQL en Coolify
DB_USER=tu_usuario    # Usuario de MySQL
DB_PASSWORD=tu_clave  # Contraseña de MySQL
DB_NAME=prime         # Nombre de la base de datos
```

Si estás utilizando un servicio MySQL existente, configura DB_HOST con la dirección IP o nombre de host correspondiente.

### 5. Configurar persistencia

Añade los siguientes volúmenes para garantizar que los datos se persistan entre despliegues:

1. Volumen para uploads:
   - Ruta del contenedor: `/var/www/html/uploads`
   - Directorio local o volumen de Coolify

2. Volumen para uploads de administración:
   - Ruta del contenedor: `/var/www/html/admin/uploads`
   - Directorio local o volumen de Coolify

3. Volumen para logs:
   - Ruta del contenedor: `/var/www/html/logs`
   - Directorio local o volumen de Coolify

### 6. Configurar red

1. Si estás utilizando un servicio MySQL existente:
   - Asegúrate de que la red de Coolify permita la conexión a tu host de MySQL

### 7. Configurar base de datos

Si necesitas crear una nueva base de datos para el proyecto:

1. En Coolify, crea un nuevo recurso tipo "Service"
2. Selecciona "MySQL" o "MariaDB"
3. Configura el usuario, contraseña y nombre de la base de datos
4. Establece la red apropiada para que tu aplicación pueda acceder a ella
5. Una vez desplegado, importa el esquema de base de datos usando la terminal:

```bash
# Accede a la terminal del contenedor MySQL en Coolify
mysql -u root -p

# Dentro de la consola MySQL, crea la base de datos
CREATE DATABASE prime;
USE prime;

# Luego puedes importar el esquema de database/schema.sql
# Salir de la consola MySQL
exit

# Desde la terminal del contenedor MySQL
mysql -u root -p prime < /ruta/a/schema.sql
```

Alternativamente, puedes conectarte a la base de datos desde tu aplicación favorita de gestión de MySQL usando los detalles proporcionados por Coolify.

### 8. Desplegar la aplicación

1. Haz clic en "Deploy" o "Desplegar"
2. Espera a que el proceso de compilación y despliegue finalice
3. Una vez completado, haz clic en la URL proporcionada por Coolify para acceder a tu aplicación

### 9. Verificar la instalación

1. Accede a la URL proporcionada
2. Si todo está configurado correctamente, deberías ver la pantalla de inicio de sesión
3. Utiliza las credenciales predeterminadas para iniciar sesión:
   - Email: admin@primeinstinct.com
   - Contraseña: admin123

4. Accede a la página de diagnóstico para verificar que todo funciona correctamente:
   - Visita: `tu-url/diagnostico.php`
   - Contraseña de diagnóstico: `prime2025`

## Solución de problemas

Si encuentras problemas durante el despliegue:

1. **Problemas de base de datos**:
   - Comprueba la conexión a la base de datos con la herramienta de diagnóstico
   - Verifica las variables de entorno de la base de datos

2. **Problemas de permisos**:
   - Asegúrate de que los directorios `uploads`, `admin/uploads` y `logs` tengan permisos adecuados
   - Puedes ejecutar en la terminal de Coolify: `chmod -R 777 /var/www/html/uploads /var/www/html/admin/uploads /var/www/html/logs`

3. **Página de diagnóstico**:
   - Usa la página de diagnóstico en `tu-url/diagnostico.php` para verificar el estado del sistema

## Configuraciones avanzadas

### Para acceder desde internet:

1. **Configurar DuckDNS** (como se explicó en conversaciones anteriores)
2. **Configurar reenvío de puertos** en tu router apuntando al puerto de Coolify

### Cambiar contraseña de administrador:

1. Accede a la terminal de tu contenedor MySQL en Coolify
2. Genera un hash de contraseña nueva:
   ```php
   php -r "echo password_hash('nueva_contraseña', PASSWORD_DEFAULT);"
   ```
3. Ejecuta el siguiente comando SQL:
   ```sql
   USE prime;
   UPDATE usuario SET contraseña='hash_generado' WHERE id=1;
   ```

### HTTPS:

Para configurar HTTPS en Coolify:
1. Configura un dominio en la configuración de la aplicación
2. Activa la opción "Enable HTTPS" o similar en Coolify

## Contacto y soporte

Si necesitas ayuda adicional, contacta a tu administrador de sistemas o consulta la documentación de Coolify en https://coolify.io/docs/.
