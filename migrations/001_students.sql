-- 001_students.sql
-- Adds the academic 'Student' identity to the auth server.
-- Idempotent: safe to re-run against an existing live database.

USE local_auth_db;

-- 1. Rename the 'Normal User' role to 'Student'
UPDATE roles
   SET role_name = 'Student',
       description = 'Student with access to their own dashboard and profile only'
 WHERE role_name = 'Normal User';

-- 2. Student profile table (1:1 with users)
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  student_id VARCHAR(20) NOT NULL UNIQUE,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  department VARCHAR(100) NOT NULL,
  level VARCHAR(20) NOT NULL,
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Seed the sample account (user1) as a student
UPDATE users SET full_name = 'Hassan Abdullahi' WHERE username = 'user1';

INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
SELECT u.id, 'CSC/2023/0021', 'Hassan', 'Abdullahi', u.email, 'Computer Science', '400 Level', '+234 803 555 0192'
  FROM users u
 WHERE u.username = 'user1'
ON DUPLICATE KEY UPDATE
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  email = VALUES(email),
  department = VALUES(department),
  level = VALUES(level),
  phone = VALUES(phone);