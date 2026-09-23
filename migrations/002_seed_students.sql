-- 002_seed_students.sql
-- Seeds ~10 lab student accounts + their student profiles.
-- Default password for all seeded students: Student@123
-- Idempotent: existing usernames / student IDs are left untouched.

USE local_auth_db;

SET NAMES latin1;

INSERT IGNORE INTO users (username, email, full_name, password_hash, role_id, is_active)
SELECT s.username, s.email, s.full_name, s.hash, r.id, 1
  FROM (
    SELECT 'aisha1'    AS username, 'aisha1@local.test'    AS email, 'Aisha Bello'       AS full_name, '$2y$10$zDJOLv.C6XwyryHECxpj7e13fcPewKKNCg1cjU/NiiaMRoTLjoyIq' AS hash
    UNION ALL SELECT 'ibrahim1',  'ibrahim1@local.test',  'Ibrahim Musa',     '$2y$10$vaRpR4a154ctwqXm6.KuHuJm1DHmg0mfVlGZCMgM9QHWW8m4PxBGS'
    UNION ALL SELECT 'chidera1',  'chidera1@local.test',  'Chidera Okafor',   '$2y$10$jrpx.V7.8Q6Hh0y22OQhTeNUVYYydyngUZUhIfjUsGnp1hx3DFSZW'
    UNION ALL SELECT 'emmanuel1', 'emmanuel1@local.test', 'Emmanuel Adeyemi', '$2y$10$FvLcJtvWRTsLlI3rc59LeuuFbBdU7O7iyRBwGrzQncxZaYfxNlznu'
    UNION ALL SELECT 'fatima1',   'fatima1@local.test',   'Fatima Bello',     '$2y$10$dhGMBZ5D0jX/ASIQWd6k8.TGzyap4gECSOJWjU.w4oTfItxr6ft1i'
    UNION ALL SELECT 'samuel1',   'samuel1@local.test',   'Samuel Eze',       '$2y$10$lOp2Fm63Dg.BXoVWaL0FvOVOOmr8exAyZBIXbTzAYwM8djlzk2pou'
    UNION ALL SELECT 'blessing1', 'blessing1@local.test', 'Blessing Uche',    '$2y$10$s0KlO3s6CTAoyROlNHguL.rLHZB0FeF5GxgsUQt/0GmofLBt9LBk.'
    UNION ALL SELECT 'john1',     'john1@local.test',     'John Agboola',     '$2y$10$mpurZcZ3FegAlOURMcvXNu8NCrVIasGX5PFQ2dkGT4OlbYwlXMNQS'
    UNION ALL SELECT 'maryam1',   'maryam1@local.test',   'Maryam Lawal',     '$2y$10$lczMxilOjHTesyASjRjsdOmoreHSkrncnZcQvMPnor5aBlTRTMR/q'
    UNION ALL SELECT 'david1',    'david1@local.test',    'David Nwosu',      '$2y$10$ESCC52/3YgWi1hI/LZx/5uJECqGYfktoQEVFBhGwgSim2lZYbV24y'
  ) s
  CROSS JOIN roles r
 WHERE r.role_name = 'Student';

INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
SELECT u.id, p.student_id, p.first_name, p.last_name, u.email, p.department, p.level, p.phone
  FROM (
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
  ) p
  JOIN users u ON u.username = p.username
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  department = VALUES(department),
  level = VALUES(level),
  phone = VALUES(phone);