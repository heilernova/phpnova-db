select replace(replace(replace(replace(column_type, 'enum', ''), '(', '['), ')', ']'), "'", '"') AS roles from information_schema.COLUMNS where TABLE_NAME = 'tb_businesses_branch_users' AND column_name = 'role';