CREATE DATABASE IF NOT EXISTS local_auth_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE local_auth_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS login_logs;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  permission_name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  UNIQUE KEY uq_role_permission (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE students (
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

CREATE TABLE login_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  username_attempted VARCHAR(50) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent VARCHAR(255),
  success TINYINT(1) NOT NULL DEFAULT 0,
  logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_username (username_attempted),
  KEY idx_logged_at (logged_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (role_name, description) VALUES
('Administrator', 'Full access to all users, roles, permissions and authentication logs'),
('Network Technician', 'Can view users and authentication logs; cannot modify accounts'),
('Student', 'Student with access to their own dashboard and profile only');

INSERT INTO permissions (permission_name, description) VALUES
('dashboard.view', 'Access the dashboard'),
('profile.edit', 'Edit own profile and change own password'),
('users.view', 'View the user list'),
('users.create', 'Create new user accounts'),
('users.edit', 'Edit user account details'),
('users.manage', 'Activate or deactivate users and reset passwords'),
('roles.manage', 'Create, edit and delete roles'),
('permissions.assign', 'Assign permissions to roles'),
('logs.view', 'View authentication logs');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p
WHERE r.role_name = 'Administrator';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.permission_name IN ('dashboard.view', 'profile.edit', 'users.view', 'logs.view')
WHERE r.role_name = 'Network Technician';

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r
JOIN permissions p ON p.permission_name IN ('dashboard.view', 'profile.edit')
WHERE r.role_name = 'Student';

INSERT INTO users (username, email, full_name, password_hash, role_id, is_active) VALUES
('admin', 'admin@local.test', 'System Administrator', '$2y$10$yyBBbWocsMOU7hbCNzgbjObGBFE9TcdecD1bCwyPWMhbMR4QfCpam', (SELECT id FROM roles WHERE role_name = 'Administrator'), 1),
('tech1', 'tech1@local.test', 'Network Technician One', '$2y$10$tKE0Z8xfM60WTTlR4pZtFeqd7ZYrDqXKo5wCp9tW4Y.h6M4/Mcwlq', (SELECT id FROM roles WHERE role_name = 'Network Technician'), 1),
('user1', 'user1@local.test', 'Hassan Abdullahi', '$2y$10$66Vte/XMzslbeeLlV4f/huTfxzww4eCzsuseTfxhq5RXuYIDPvv0W', (SELECT id FROM roles WHERE role_name = 'Student'), 1);

INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
SELECT u.id, 'CSC/2023/0021', 'Hassan', 'Abdullahi', u.email, 'Computer Science', '400 Level', '+234 803 555 0192'
  FROM users u WHERE u.username = 'user1';

-- Additional lab students (default password: Student@123)
INSERT INTO users (username, email, full_name, password_hash, role_id, is_active) VALUES
('aisha1',    'aisha1@local.test',    'Aisha Bello',       '$2y$10$zDJOLv.C6XwyryHECxpj7e13fcPewKKNCg1cjU/NiiaMRoTLjoyIq', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('ibrahim1',  'ibrahim1@local.test',  'Ibrahim Musa',      '$2y$10$vaRpR4a154ctwqXm6.KuHuJm1DHmg0mfVlGZCMgM9QHWW8m4PxBGS', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('chidera1',  'chidera1@local.test',  'Chidera Okafor',    '$2y$10$jrpx.V7.8Q6Hh0y22OQhTeNUVYYydyngUZUhIfjUsGnp1hx3DFSZW', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('emmanuel1', 'emmanuel1@local.test', 'Emmanuel Adeyemi',  '$2y$10$FvLcJtvWRTsLlI3rc59LeuuFbBdU7O7iyRBwGrzQncxZaYfxNlznu', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('fatima1',   'fatima1@local.test',   'Fatima Bello',      '$2y$10$dhGMBZ5D0jX/ASIQWd6k8.TGzyap4gECSOJWjU.w4oTfItxr6ft1i', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('samuel1',   'samuel1@local.test',   'Samuel Eze',        '$2y$10$lOp2Fm63Dg.BXoVWaL0FvOVOOmr8exAyZBIXbTzAYwM8djlzk2pou', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('blessing1', 'blessing1@local.test', 'Blessing Uche',     '$2y$10$s0KlO3s6CTAoyROlNHguL.rLHZB0FeF5GxgsUQt/0GmofLBt9LBk.', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('john1',     'john1@local.test',     'John Agboola',      '$2y$10$mpurZcZ3FegAlOURMcvXNu8NCrVIasGX5PFQ2dkGT4OlbYwlXMNQS', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('maryam1',   'maryam1@local.test',   'Maryam Lawal',      '$2y$10$lczMxilOjHTesyASjRjsdOmoreHSkrncnZcQvMPnor5aBlTRTMR/q', (SELECT id FROM roles WHERE role_name = 'Student'), 1),
('david1',    'david1@local.test',    'David Nwosu',       '$2y$10$ESCC52/3YgWi1hI/LZx/5uJECqGYfktoQEVFBhGwgSim2lZYbV24y', (SELECT id FROM roles WHERE role_name = 'Student'), 1);

INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
SELECT u.id, p.student_id, p.first_name, p.last_name, u.email, p.department, p.level, p.phone
  FROM users u
  JOIN (
    SELECT 'aisha1' AS username, 'CSC/2022/0031' AS student_id, 'Aisha' AS first_name, 'Bello' AS last_name, 'Computer Science' AS department, '300 Level' AS level, '+234 802 111 2233' AS phone
    UNION ALL SELECT 'ibrahim1', 'SWE/2022/0017',  'Ibrahim',  'Musa',    'Software Engineering',   '300 Level', '+234 803 222 3344'
    UNION ALL SELECT 'chidera1', 'IT/2023/0045',   'Chidera',  'Okafor',  'Information Technology', '200 Level', '+234 814 333 4455'
    UNION ALL SELECT 'emmanuel1', 'EEE/2021/0028', 'Emmanuel', 'Adeyemi', 'Electrical Engineering', '400 Level', '+234 805 444 5566'
    UNION ALL SELECT 'fatima1',  'CYS/2023/0009',  'Fatima',   'Bello',   'Cyber Security',         '200 Level', '+234 816 555 6677'
    UNION ALL SELECT 'samuel1',  'MEE/2022/0036',  'Samuel',   'Eze',     'Mechanical Engineering', '300 Level', '+234 806 666 7788'
    UNION ALL SELECT 'blessing1', 'CSC/2021/0102', 'Blessing', 'Uche',    'Computer Science',       '400 Level', '+234 807 777 8899'
    UNION ALL SELECT 'john1',    'IT/2021/0071',   'John',     'Agboola', 'Information Technology', '400 Level', '+234 808 888 9900'
    UNION ALL SELECT 'maryam1',  'SWE/2023/0053',  'Maryam',   'Lawal',   'Software Engineering',   '200 Level', '+234 809 999 0011'
    UNION ALL SELECT 'david1',   'CYS/2022/0014',  'David',    'Nwosu',   'Cyber Security',         '300 Level', '+234 810 000 1122'
  ) p ON p.username = u.username;