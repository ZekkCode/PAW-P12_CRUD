CREATE DATABASE IF NOT EXISTS db_crud_students;
USE db_crud_students;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nim VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  major VARCHAR(100) NOT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO students (nim, name, major, photo) VALUES
('240411100101', 'Andi Pratama', 'Teknik Informatika', 'profesional.jpg'),
('240411100102', 'Budi Santoso', 'Sistem Informasi', 'profesional.jpg'),
('240411100103', 'Citra Rahmawati', 'Teknik Komputer', 'profesional.jpg'),
('240411100104', 'Dewi Lestari', 'Manajemen Informatika', 'profesional.jpg'),
('240411100105', 'Eka Saputra', 'Teknologi Informasi', 'profesional.jpg'),
('240411100106', 'Farhan Malik', 'Teknik Komputer', 'profesional.jpg'),
('240411100107', 'Gita Wulandari', 'Sistem Informasi', 'profesional.jpg'),
('240411100108', 'Hadi Setiawan', 'Teknik Informatika', 'profesional.jpg'),
('240411100109', 'Intan Permata', 'Manajemen Informatika', 'profesional.jpg'),
('240411100110', 'Joko Triyono', 'Teknologi Informasi', 'profesional.jpg');
