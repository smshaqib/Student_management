```
CREATE DATABASE student_manager;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    student_id VARCHAR(100),
    department VARCHAR(100),
    major VARCHAR(100),
    dob DATE,
    address TEXT
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(100),
    course_code VARCHAR(100),
    course_title VARCHAR(255),
    semester VARCHAR(50)
);
```
