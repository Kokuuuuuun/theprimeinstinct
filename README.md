# Prime Instinct - Tienda de Calzado Deportivo

## Descripción
Prime Instinct es una tienda en línea especializada en calzado deportivo de alta calidad.

## Despliegue en Coolify

### Prerrequisitos
- Coolify instalado en tu servidor local
- Docker y Docker Compose instalados
- MySQL o MariaDB

### Pasos para desplegar en Coolify

1. **Prepara tu entorno Coolify**
   - Asegúrate de que Coolify esté instalado y funcionando en tu servidor.
   - Accede a la interfaz web de Coolify.

2. **Crear un nuevo proyecto**
   - En el panel de Coolify, selecciona "Crear nuevo recurso".
   - Elige "Aplicación" como tipo de recurso.

3. **Configuración del proyecto**
   - Elige "Docker" como tipo de aplicación.
   - Conecta tu repositorio Git o usa la opción de directorio local si vas a subir los archivos manualmente.
   - Si usas Git, configura la rama (normalmente `main` o `master`).

4. **Configuración de la compilación**
   - Usa el Dockerfile incluido en este proyecto.
   - Directorio de trabajo: `/var/www/html`
   - Comando de inicio: `./coolify-init.sh` (asegúrate de que el archivo tenga permisos de ejecución)

5. **Variables de entorno**
   Configura las siguientes variables de entorno en Coolify:
   ```
   DB_HOST=tu_host_mysql
   DB_USER=tu_usuario_mysql
   DB_PASSWORD=tu_contraseña_mysql
   DB_NAME=prime
   ```

6. **Configuración de persistencia**
   Agrega un volumen para la carpeta `/var/www/html/uploads` para que los archivos subidos no se pierdan entre despliegues.

7. **Configuración de la base de datos**
   - Si estás utilizando la base de datos integrada de Coolify, crea una nueva instancia MySQL.
   - Importa el archivo `database/schema.sql` para crear la estructura inicial de la base de datos.
   - Conecta tu aplicación con la base de datos usando las variables de entorno.

8. **Configuración de dominio**
   - En Coolify, configura el dominio para tu aplicación.
   - Puedes usar un subdominio, dominio personalizado o el dominio generado por Coolify.
   - Si usas un dominio personalizado, configura los registros DNS correspondientes.

9. **Habilitar HTTPS (recomendado)**
   - Activa la opción de HTTPS en Coolify para obtener un certificado SSL automáticamente.

10. **Despliega la aplicación**
    - Inicia el despliegue desde la interfaz de Coolify.
    - Monitorea los logs para asegurarte de que todo funciona correctamente.

## Acceso al sistema

Una vez desplegada la aplicación, puedes acceder con las siguientes credenciales:

- **Administrador**:
  - Email: admin@primeinstinct.com
  - Contraseña: admin123

## Estructura del proyecto

- `/` - Archivos principales
- `/admin` - Panel de administración
- `/php` - Archivos PHP para la lógica de negocio
- `/src` - Recursos (CSS, JS)
- `/img` - Imágenes del sitio
- `/uploads` - Archivos subidos por los usuarios
- `/database` - Scripts de base de datos

## Mantenimiento

### Actualizaciones
Para actualizar la aplicación, simplemente haz pull de los cambios en tu repositorio Git o actualiza los archivos en tu directorio local y Coolify se encargará de reconstruir y desplegar la aplicación.

### Copias de seguridad
Configura copias de seguridad regulares de la base de datos y los archivos subidos por los usuarios en `/var/www/html/uploads`.

## Solución de problemas

### Logs
Revisa los logs de la aplicación en Coolify para identificar problemas.

### Problemas comunes
- **Error de conexión a la base de datos**: Verifica las variables de entorno para la conexión a la base de datos.
- **Problemas de permisos en uploads**: Asegúrate de que el directorio `/var/www/html/uploads` tenga permisos de escritura.
- **Error 500**: Revisa los logs de Apache para más detalles.

## Soporte

Para soporte técnico, contacta a [tu@email.com].

---

© 2025 Prime Instinct. Todos los derechos reservados.
