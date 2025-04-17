#!/bin/bash
set -e

# Script de inicialización para Coolify
echo "🚀 Iniciando Prime Instinct en Coolify..."

# Verificar variables de entorno necesarias
echo "✅ Verificando variables de entorno..."
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ]; then
  echo "⚠️ Advertencia: Variables de entorno DB_HOST o DB_NAME no definidas. Usando valores por defecto."
fi

# Verificar conexión a la base de datos
echo "🔄 Verificando conexión a la base de datos..."
max_attempts=30
counter=0
while ! mysqladmin ping -h"${DB_HOST:-localhost}" -u"${DB_USER:-root}" -p"${DB_PASSWORD:-}" --silent 2>/dev/null && [ $counter -lt $max_attempts ]; do
  echo "⌛ Esperando a que la base de datos esté disponible... ($counter/$max_attempts)"
  sleep 2
  counter=$((counter+1))
done

if [ $counter -eq $max_attempts ]; then
  echo "⚠️ No se pudo conectar a la base de datos después de $max_attempts intentos. Continuando de todos modos..."
else
  echo "✅ Conexión a la base de datos establecida."
fi

# Verificar directorios y permisos
echo "🔄 Verificando directorios y permisos..."
for dir in uploads uploads/products admin/uploads logs; do
  if [ ! -d "/var/www/html/$dir" ]; then
    echo "📁 Creando directorio $dir"
    mkdir -p "/var/www/html/$dir"
  fi
  echo "🔐 Estableciendo permisos para $dir"
  chmod -R 777 "/var/www/html/$dir"
done
chown -R www-data:www-data /var/www/html

# Verificar existencia de la base de datos y crear esquema si es necesario
if [ ! -z "$DB_HOST" ] && [ ! -z "$DB_NAME" ] && [ ! -z "$DB_USER" ]; then
  echo "🔄 Verificando esquema de la base de datos..."
  if mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASSWORD}" -e "USE ${DB_NAME};" 2>/dev/null; then
    echo "✅ Base de datos ${DB_NAME} existe."
  else
    echo "🔄 Creando esquema de base de datos..."
    if [ -f "/var/www/html/database/schema.sql" ]; then
      mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASSWORD}" < "/var/www/html/database/schema.sql"
      echo "✅ Esquema de base de datos creado."
    else
      echo "⚠️ No se encontró el archivo schema.sql. Omitiendo creación de esquema."
    fi
  fi
fi

echo "🚀 Prime Instinct está listo. Iniciando servidor Apache..."

# Iniciar Apache en primer plano
exec apache2-foreground
