SELECT
  kcu.TABLE_NAME           AS table_name,
  kcu.COLUMN_NAME          AS column_name,
  kcu.REFERENCED_TABLE_NAME AS referenced_table,
  kcu.REFERENCED_COLUMN_NAME AS referenced_column,
  rc.UPDATE_RULE           AS on_update,
  rc.DELETE_RULE           AS on_delete,
  kcu.CONSTRAINT_NAME      AS constraint_name
FROM information_schema.KEY_COLUMN_USAGE kcu
JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
  ON rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
 AND rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
WHERE kcu.CONSTRAINT_SCHEMA = DATABASE()
  AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY kcu.TABLE_NAME, kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION;