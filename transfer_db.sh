#!/bin/bash

# Параметры первой базы данных (источник)
SOURCE_DB_HOST="localhost"
SOURCE_DB_USER="u1081260_finwp3"
SOURCE_DB_NAME="u1081260_finwp3"
SOURCE_DB_PASS="rP6yN6vQ4sxO1eQ0"

# Параметры второй базы данных (цель)
TARGET_DB_HOST="localhost"
TARGET_DB_USER="u1081260_stagefi"
TARGET_DB_NAME="u1081260_stagefindv"
TARGET_DB_PASS="hM5xH5qJ9xiY3aG6"

# Создание дампа первой базы данных
DUMP_FILE="/var/www/u1081260/data/backups/source_db_dump.sql"
mysqldump -h "$SOURCE_DB_HOST" --no-tablespaces --column-statistics=0 -u "$SOURCE_DB_USER" -p"$SOURCE_DB_PASS" "$SOURCE_DB_NAME" > "$DUMP_FILE"

# Очистка второй базы данных
mysql -h "$TARGET_DB_HOST" -u "$TARGET_DB_USER" -p"$TARGET_DB_PASS" "$TARGET_DB_NAME" -e "SET FOREIGN_KEY_CHECKS=0; DROP DATABASE $TARGET_DB_NAME; CREATE DATABASE $TARGET_DB_NAME; SET FOREIGN_KEY_CHECKS=1;"

# Импорт дампа в вторую базу данных
mysql -h "$TARGET_DB_HOST" -u "$TARGET_DB_USER" -p"$TARGET_DB_PASS" "$TARGET_DB_NAME" < "$DUMP_FILE"

# Удаление временного файла дампа
rm -f "$DUMP_FILE"

echo "Перенос завершен."