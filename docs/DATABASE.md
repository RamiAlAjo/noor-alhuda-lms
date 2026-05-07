# Noor Alhuda LMS - Database Schema Documentation

This document provides comprehensive documentation of the Noor Alhuda LMS database schema, including all tables, columns, relationships, and constraints.

---

## Table of Contents

1. [Database Overview](#database-overview)
2. [Entity-Relationship Model](#entity-relationship-model)
3. [Table Definitions](#table-definitions)
4. [Indexes](#indexes)
5. [Relationships Summary](#relationships-summary)
6. [Database Setup](#database-setup)
7. [Database Seeding](#database-seeding)
8. [Backup & Restore](#backup--restore)
9. [Performance Considerations](#performance-considerations)
10. [Migration Reference](#migration-reference)

---

## Database Overview

| Property           | Value              |
| ------------------ | ------------------ |
| **Database Type**  | MySQL / SQLite     |
| **Character Set**  | utf8mb4            |
| **Collation**      | utf8mb4_unicode_ci |
| **Storage Engine** | InnoDB (MySQL)     |

---

## Entity-Relationship Model

The database follows a normalized relational model with the following main entities:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           CORE ENTITIES                                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌──────────┐     ┌──────────────┐     ┌─────────────┐                      │
│  │  Users   │────▶│User_Profiles │     │User_Settings│                      │
│  └──────────┘     └──────────────┘     └─────────────┘                      │
│       │                                                                   │
│       │ (role-based)                                                       │
│       ▼                                                                   │
│  ┌─────────────┐     ┌─────────────┐     ┌───────────┐                    │
│  │  Academic   │────▶│  Semesters  │────▶│ Majors    │                    │
│  │  Years      │     │             │     │           │                    │
│  └─────────────┘     └─────────────┘     └───────────┘                    │
│        │                   │                   │                             │
│        │                   ▼                   ▼                             │
│        │            ┌─────────────┐     ┌───────────┐     ┌──────────┐    │
│        │            │ Departments│────▶│ Courses   │────▶│Course    │    │
│        │            └─────────────┘     └───────────┘     │Sections  │    │
│        │                                        │         └──────────┘    │
│        │                                        ▼                         │
│        │            ┌─────────────┐     ┌───────────┐     ┌──────────┐    │
│        └──────────▶│  Enrollments│◀────│  Fees     │◀────│ Payments │    │
│                     └─────────────┘     └───────────┘     └──────────┘    │
│                            │                                                   │
│                            ▼                                                   │
│                     ┌─────────────┐     ┌─────────────┐                     │
│                     │    Grades   │────▶│   Grade     │                     │
│                     │             │     │   Appeals   │                     │
│                     └─────────────┘     └─────────────┘                     │
│                            │                                                   │
│                            ▼                                                   │
│                     ┌─────────────┐     ┌─────────────┐                     │
│                     │Attendance   │     │Medical      │                     │
│                     │Records      │     │Leaves      │                     │
│                     └─────────────┘     └─────────────┘                     │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Table Definitions

### Users & Authentication

#### users

The core authentication table containing all system users.

| Column                      | Type            | Nullable | Default        | Constraints |
| --------------------------- | --------------- | -------- | -------------- | ----------- |
| `id`                        | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY |
| `name`                      | VARCHAR(255)    | No       | -              | -           |
| `email`                     | VARCHAR(255)    | No       | -              | UNIQUE      |
| `email_verified_at`         | TIMESTAMP       | Yes      | NULL           | -           |
| `password`                  | VARCHAR(255)    | No       | -              | -           |
| `remember_token`            | VARCHAR(100)    | Yes      | NULL           | -           |
| `user_id`                   | VARCHAR(50)     | Yes      | NULL           | UNIQUE      |
| `two_factor_secret`         | TEXT            | Yes      | NULL           | -           |
| `two_factor_recovery_codes` | TEXT            | Yes      | NULL           | -           |
| `two_factor_confirmed_at`   | TIMESTAMP       | Yes      | NULL           | -           |
| `current_team_id`           | BIGINT UNSIGNED | Yes      | NULL           | -           |
| `profile_photo_path`        | VARCHAR(2048)   | Yes      | NULL           | -           |
| `is_active`                 | TINYINT(1)      | No       | 1              | -           |
| `created_at`                | TIMESTAMP       | Yes      | NULL           | -           |
| `updated_at`                | TIMESTAMP       | Yes      | NULL           | -           |

> **Note**: Uses Laravel's default users table with Spatie Permission integration.

---

#### user_profiles

Extended profile information for users.

| Column             | Type                   | Nullable | Default        | Constraints            |
| ------------------ | ---------------------- | -------- | -------------- | ---------------------- |
| `id`               | BIGINT UNSIGNED        | No       | AUTO_INCREMENT | PRIMARY KEY            |
| `user_id`          | BIGINT UNSIGNED        | No       | -              | FOREIGN KEY → users.id |
| `user_id_unique`   | VARCHAR(50)            | Yes      | NULL           | UNIQUE                 |
| `first_name`       | VARCHAR(255)           | No       | -              | -                      |
| `second_name`      | VARCHAR(255)           | Yes      | NULL           | -                      |
| `third_name`       | VARCHAR(255)           | Yes      | NULL           | -                      |
| `last_name`        | VARCHAR(255)           | No       | -              | -                      |
| `phone`            | VARCHAR(50)            | Yes      | NULL           | -                      |
| `address`          | TEXT                   | Yes      | NULL           | -                      |
| `date_of_birth`    | DATE                   | Yes      | NULL           | -                      |
| `gender`           | ENUM('male', 'female') | Yes      | NULL           | -                      |
| `photo`            | VARCHAR(255)           | Yes      | NULL           | -                      |
| `bio`              | TEXT                   | Yes      | NULL           | -                      |
| `initial_password` | VARCHAR(255)           | Yes      | NULL           | -                      |
| `created_at`       | TIMESTAMP              | Yes      | NULL           | -                      |
| `updated_at`       | TIMESTAMP              | Yes      | NULL           | -                      |

**Relationships:**

- `user_id` → [`users`](#users) (One-to-One, ON DELETE CASCADE)

---

#### user_settings

User-specific application settings and preferences.

| Column                     | Type            | Nullable | Default        | Constraints            |
| -------------------------- | --------------- | -------- | -------------- | ---------------------- |
| `id`                       | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY            |
| `user_id`                  | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id |
| `theme`                    | VARCHAR(50)     | No       | 'zinc'         | -                      |
| `base_theme`               | VARCHAR(50)     | No       | 'default-dark' | -                      |
| `appearance`               | VARCHAR(20)     | No       | 'dark'         | -                      |
| `accent_color`             | VARCHAR(20)     | No       | 'zinc'         | -                      |
| `locale`                   | VARCHAR(10)     | No       | 'en'           | -                      |
| `high_contrast`            | TINYINT(1)      | No       | 0              | -                      |
| `large_text`               | TINYINT(1)      | No       | 0              | -                      |
| `dyslexia_font`            | TINYINT(1)      | No       | 0              | -                      |
| `reduced_motion`           | TINYINT(1)      | No       | 0              | -                      |
| `grayscale`                | TINYINT(1)      | No       | 0              | -                      |
| `strong_focus_outline`     | TINYINT(1)      | No       | 0              | -                      |
| `line_spacing`             | FLOAT           | No       | 1.5            | -                      |
| `gradient_background`      | VARCHAR(50)     | Yes      | NULL           | -                      |
| `notification_preferences` | JSON            | Yes      | NULL           | -                      |
| `created_at`               | TIMESTAMP       | Yes      | NULL           | -                      |
| `updated_at`               | TIMESTAMP       | Yes      | NULL           | -                      |

**Relationships:**

- `user_id` → [`users`](#users) (One-to-One, ON DELETE CASCADE)

---

#### password_reset_tokens

Password reset token storage.

| Column       | Type         | Nullable | Constraints |
| ------------ | ------------ | -------- | ----------- |
| `email`      | VARCHAR(255) | No       | PRIMARY KEY |
| `token`      | VARCHAR(255) | No       | -           |
| `created_at` | TIMESTAMP    | Yes      | -           |

---

#### sessions

User session storage.

| Column          | Type            | Nullable | Constraints |
| --------------- | --------------- | -------- | ----------- |
| `id`            | VARCHAR(255)    | No       | PRIMARY KEY |
| `user_id`       | BIGINT UNSIGNED | Yes      | INDEX       |
| `ip_address`    | VARCHAR(45)     | Yes      | -           |
| `user_agent`    | TEXT            | Yes      | -           |
| `payload`       | LONGTEXT        | No       | -           |
| `last_activity` | INT             | No       | INDEX       |

---

### Academic Structure

#### academic_years

Academic year definitions.

| Column       | Type            | Nullable | Default        | Constraints |
| ------------ | --------------- | -------- | -------------- | ----------- |
| `id`         | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY |
| `name`       | VARCHAR(255)    | No       | -              | -           |
| `start_year` | YEAR            | No       | -              | -           |
| `end_year`   | YEAR            | No       | -              | -           |
| `is_current` | TINYINT(1)      | No       | 0              | -           |
| `is_active`  | TINYINT(1)      | No       | 1              | -           |
| `created_at` | TIMESTAMP       | Yes      | NULL           | -           |
| `updated_at` | TIMESTAMP       | Yes      | NULL           | -           |

---

#### semesters

Semester definitions within academic years.

| Column             | Type            | Nullable | Default        | Constraints                     |
| ------------------ | --------------- | -------- | -------------- | ------------------------------- |
| `id`               | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                     |
| `academic_year_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → academic_years.id |
| `name`             | VARCHAR(255)    | No       | -              | -                               |
| `name_ar`          | VARCHAR(255)    | Yes      | NULL           | -                               |
| `start_date`       | DATE            | No       | -              | -                               |
| `end_date`         | DATE            | No       | -              | -                               |
| `enrollment_start` | DATE            | Yes      | NULL           | -                               |
| `enrollment_end`   | DATE            | Yes      | NULL           | -                               |
| `is_current`       | TINYINT(1)      | No       | 0              | -                               |
| `is_active`        | TINYINT(1)      | No       | 1              | -                               |
| `created_at`       | TIMESTAMP       | Yes      | NULL           | -                               |
| `updated_at`       | TIMESTAMP       | Yes      | NULL           | -                               |

**Relationships:**

- `academic_year_id` → [`academic_years`](#academic_years) (Many-to-One, ON DELETE CASCADE)

---

#### faculties

Faculty/College definitions.

| Column           | Type            | Nullable | Default        | Constraints |
| ---------------- | --------------- | -------- | -------------- | ----------- |
| `id`             | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY |
| `name`           | VARCHAR(255)    | No       | -              | -           |
| `name_ar`        | VARCHAR(255)    | Yes      | NULL           | -           |
| `code`           | VARCHAR(50)     | No       | -              | UNIQUE      |
| `description`    | TEXT            | Yes      | NULL           | -           |
| `description_ar` | TEXT            | Yes      | NULL           | -           |
| `dean_name`      | VARCHAR(255)    | Yes      | NULL           | -           |
| `email`          | VARCHAR(255)    | Yes      | NULL           | -           |
| `phone`          | VARCHAR(50)     | Yes      | NULL           | -           |
| `photo`          | VARCHAR(255)    | Yes      | NULL           | -           |
| `is_active`      | TINYINT(1)      | No       | 1              | -           |
| `created_at`     | TIMESTAMP       | Yes      | NULL           | -           |
| `updated_at`     | TIMESTAMP       | Yes      | NULL           | -           |

---

#### departments

Department definitions within faculties.

| Column           | Type            | Nullable | Default        | Constraints                |
| ---------------- | --------------- | -------- | -------------- | -------------------------- |
| `id`             | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                |
| `faculty_id`     | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → faculties.id |
| `name`           | VARCHAR(255)    | No       | -              | -                          |
| `name_ar`        | VARCHAR(255)    | Yes      | NULL           | -                          |
| `code`           | VARCHAR(50)     | No       | -              | UNIQUE                     |
| `description`    | TEXT            | Yes      | NULL           | -                          |
| `description_ar` | TEXT            | Yes      | NULL           | -                          |
| `head_name`      | VARCHAR(255)    | Yes      | NULL           | -                          |
| `email`          | VARCHAR(255)    | Yes      | NULL           | -                          |
| `phone`          | VARCHAR(50)     | Yes      | NULL           | -                          |
| `is_active`      | TINYINT(1)      | No       | 1              | -                          |
| `created_at`     | TIMESTAMP       | Yes      | NULL           | -                          |
| `updated_at`     | TIMESTAMP       | Yes      | NULL           | -                          |

**Relationships:**

- `faculty_id` → [`faculties`](#faculties) (Many-to-One, ON DELETE CASCADE)

---

#### majors

Major/Program definitions within departments.

| Column             | Type            | Nullable | Default        | Constraints                  |
| ------------------ | --------------- | -------- | -------------- | ---------------------------- |
| `id`               | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                  |
| `department_id`    | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → departments.id |
| `name`             | VARCHAR(255)    | No       | -              | -                            |
| `name_ar`          | VARCHAR(255)    | Yes      | NULL           | -                            |
| `code`             | VARCHAR(50)     | No       | -              | UNIQUE                       |
| `description`      | TEXT            | Yes      | NULL           | -                            |
| `description_ar`   | TEXT            | Yes      | NULL           | -                            |
| `years_required`   | INT             | No       | 4              | -                            |
| `credits_required` | INT             | No       | 120            | -                            |
| `is_active`        | TINYINT(1)      | No       | 1              | -                            |
| `created_at`       | TIMESTAMP       | Yes      | NULL           | -                            |
| `updated_at`       | TIMESTAMP       | Yes      | NULL           | -                            |

**Relationships:**

- `department_id` → [`departments`](#departments) (Many-to-One, ON DELETE CASCADE)

---

### Courses

#### courses

Course definitions.

| Column               | Type            | Nullable | Default        | Constraints                         |
| -------------------- | --------------- | -------- | -------------- | ----------------------------------- |
| `id`                 | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                         |
| `department_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → departments.id        |
| `major_id`           | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → majors.id             |
| `code`               | VARCHAR(50)     | No       | -              | -                                   |
| `name`               | VARCHAR(255)    | No       | -              | -                                   |
| `name_ar`            | VARCHAR(255)    | Yes      | NULL           | -                                   |
| `description`        | TEXT            | Yes      | NULL           | -                                   |
| `description_ar`     | TEXT            | Yes      | NULL           | -                                   |
| `credits`            | INT             | No       | 3              | -                                   |
| `theory_hours`       | INT             | No       | 3              | -                                   |
| `lab_hours`          | INT             | No       | 0              | -                                   |
| `year_level`         | INT             | Yes      | NULL           | -                                   |
| `semester_available` | ENUM            | No       | 'both'         | 'first', 'second', 'summer', 'both' |
| `is_active`          | TINYINT(1)      | No       | 1              | -                                   |
| `created_at`         | TIMESTAMP       | Yes      | NULL           | -                                   |
| `updated_at`         | TIMESTAMP       | Yes      | NULL           | -                                   |

**Relationships:**

- `department_id` → [`departments`](#departments) (Many-to-One, ON DELETE CASCADE)
- `major_id` → [`majors`](#majors) (Many-to-One, ON DELETE SET NULL)

---

#### course_sections (or course_offerings)

Course section/offering definitions for specific semesters.

| Column                   | Type            | Nullable | Default        | Constraints                |
| ------------------------ | --------------- | -------- | -------------- | -------------------------- |
| `id`                     | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                |
| `course_id`              | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → courses.id   |
| `semester_id`            | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → semesters.id |
| `teacher_id`             | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id     |
| `section_name`           | VARCHAR(50)     | No       | -              | -                          |
| `max_students`           | INT             | No       | 30             | -                          |
| `current_students`       | INT             | No       | 0              | -                          |
| `room`                   | VARCHAR(100)    | Yes      | NULL           | -                          |
| `schedule`               | JSON            | Yes      | NULL           | -                          |
| `meeting_link`           | VARCHAR(500)    | Yes      | NULL           | -                          |
| `is_visible_to_students` | TINYINT(1)      | No       | 1              | -                          |
| `is_active`              | TINYINT(1)      | No       | 1              | -                          |
| `created_at`             | TIMESTAMP       | Yes      | NULL           | -                          |
| `updated_at`             | TIMESTAMP       | Yes      | NULL           | -                          |

**Unique Constraints:**

- `course_id, semester_id, section_name`

**Relationships:**

- `course_id` → [`courses`](#courses) (Many-to-One, ON DELETE CASCADE)
- `semester_id` → [`semesters`](#semesters) (Many-to-One, ON DELETE CASCADE)
- `teacher_id` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### course_materials

Course materials (lectures, assignments, resources).

| Column              | Type            | Nullable | Default        | Constraints                                          |
| ------------------- | --------------- | -------- | -------------- | ---------------------------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                          |
| `course_section_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id                     |
| `uploaded_by`       | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                               |
| `title`             | VARCHAR(255)    | No       | -              | -                                                    |
| `title_ar`          | VARCHAR(255)    | Yes      | NULL           | -                                                    |
| `description`       | TEXT            | Yes      | NULL           | -                                                    |
| `file_path`         | VARCHAR(500)    | No       | -              | -                                                    |
| `file_type`         | VARCHAR(100)    | No       | -              | -                                                    |
| `file_size`         | INT             | No       | -              | -                                                    |
| `material_type`     | ENUM            | No       | 'lecture'      | 'lecture', 'assignment', 'exam', 'resource', 'other' |
| `week`              | INT             | Yes      | NULL           | -                                                    |
| `video_url`         | VARCHAR(500)    | Yes      | NULL           | -                                                    |
| `is_published`      | TINYINT(1)      | No       | 0              | -                                                    |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                                    |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                                    |

**Relationships:**

- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `uploaded_by` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### course_teachers

Many-to-many relationship between course sections and teachers.

| Column              | Type            | Nullable | Constraints                      |
| ------------------- | --------------- | -------- | -------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | PRIMARY KEY                      |
| `course_section_id` | BIGINT UNSIGNED | No       | FOREIGN KEY → course_sections.id |
| `teacher_id`        | BIGINT UNSIGNED | No       | FOREIGN KEY → users.id           |
| `created_at`        | TIMESTAMP       | Yes      | -                                |
| `updated_at`        | TIMESTAMP       | Yes      | -                                |

**Unique Constraints:**

- `course_section_id, teacher_id`

**Relationships:**

- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `teacher_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### course_major

Many-to-many relationship between courses and majors.

| Column      | Type            | Nullable | Constraints                           |
| ----------- | --------------- | -------- | ------------------------------------- |
| `course_id` | BIGINT UNSIGNED | No       | PRIMARY KEY, FOREIGN KEY → courses.id |
| `major_id`  | BIGINT UNSIGNED | No       | PRIMARY KEY, FOREIGN KEY → majors.id  |

---

### Enrollments

#### enrollments

Student course enrollments.

| Column              | Type            | Nullable | Default        | Constraints                                  |
| ------------------- | --------------- | -------- | -------------- | -------------------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                  |
| `student_id`        | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                       |
| `course_section_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id             |
| `semester_id`       | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → semesters.id                   |
| `status`            | ENUM            | No       | 'pending'      | 'pending', 'approved', 'rejected', 'dropped' |
| `approved_by`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id                       |
| `approved_at`       | TIMESTAMP       | Yes      | NULL           | -                                            |
| `enrolled_at`       | TIMESTAMP       | Yes      | NULL           | -                                            |
| `notes`             | TEXT            | Yes      | NULL           | -                                            |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                            |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                            |

**Unique Constraints:**

- `student_id, course_section_id, semester_id`

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `semester_id` → [`semesters`](#semesters) (Many-to-One, ON DELETE CASCADE)
- `approved_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

### Academic Records

#### attendance_records

Daily attendance records for students.

| Column               | Type            | Nullable | Default        | Constraints                            |
| -------------------- | --------------- | -------- | -------------- | -------------------------------------- |
| `id`                 | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                            |
| `student_id`         | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                 |
| `course_section_id`  | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id       |
| `course_offering_id` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → course_sections.id       |
| `date`               | DATE            | No       | -              | -                                      |
| `status`             | ENUM            | No       | 'present'      | 'present', 'absent', 'excused', 'late' |
| `marked_by`          | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id                 |
| `marked_at`          | TIMESTAMP       | No       | -              | -                                      |
| `notes`              | TEXT            | Yes      | NULL           | -                                      |
| `created_at`         | TIMESTAMP       | Yes      | NULL           | -                                      |
| `updated_at`         | TIMESTAMP       | Yes      | NULL           | -                                      |

**Unique Constraints:**

- `student_id, course_section_id, date`

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `marked_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### assessment_types

Types of assessments (quiz, exam, assignment, etc.).

| Column       | Type            | Nullable | Default        | Constraints |
| ------------ | --------------- | -------- | -------------- | ----------- |
| `id`         | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY |
| `name`       | VARCHAR(255)    | No       | -              | -           |
| `name_ar`    | VARCHAR(255)    | Yes      | NULL           | -           |
| `code`       | VARCHAR(50)     | No       | -              | -           |
| `weight`     | INT             | No       | 0              | -           |
| `is_active`  | TINYINT(1)      | No       | 1              | -           |
| `created_at` | TIMESTAMP       | Yes      | NULL           | -           |
| `updated_at` | TIMESTAMP       | Yes      | NULL           | -           |

---

#### assessments

Assessment definitions (quizzes, exams, assignments).

| Column                    | Type            | Nullable | Default        | Constraints                       |
| ------------------------- | --------------- | -------- | -------------- | --------------------------------- |
| `id`                      | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                       |
| `course_section_id`       | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id  |
| `assessment_type_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → assessment_types.id |
| `title`                   | VARCHAR(255)    | No       | -              | -                                 |
| `title_ar`                | VARCHAR(255)    | Yes      | NULL           | -                                 |
| `description`             | TEXT            | Yes      | NULL           | -                                 |
| `max_grade`               | DECIMAL(5,2)    | No       | 100            | -                                 |
| `weight`                  | DECIMAL(5,2)    | Yes      | NULL           | -                                 |
| `due_date`                | DATE            | Yes      | NULL           | -                                 |
| `due_time`                | TIME            | Yes      | NULL           | -                                 |
| `duration_minutes`        | INT             | Yes      | NULL           | -                                 |
| `is_published`            | TINYINT(1)      | No       | 0              | -                                 |
| `shuffle_questions`       | TINYINT(1)      | No       | 0              | -                                 |
| `show_results`            | TINYINT(1)      | No       | 1              | -                                 |
| `allow_multiple_attempts` | TINYINT(1)      | No       | 0              | -                                 |
| `passing_score`           | DECIMAL(5,2)    | Yes      | NULL           | -                                 |
| `created_at`              | TIMESTAMP       | Yes      | NULL           | -                                 |
| `updated_at`              | TIMESTAMP       | Yes      | NULL           | -                                 |

**Relationships:**

- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `assessment_type_id` → [`assessment_types`](#assessment_types) (Many-to-One, ON DELETE CASCADE)

---

#### student_grades

Student grades for assessments.

| Column            | Type            | Nullable | Default        | Constraints                  |
| ----------------- | --------------- | -------- | -------------- | ---------------------------- |
| `id`              | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                  |
| `student_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id       |
| `assessment_id`   | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → assessments.id |
| `enrollment_id`   | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → enrollments.id |
| `grade`           | DECIMAL(5,2)    | Yes      | NULL           | -                            |
| `grade_points`    | DECIMAL(3,2)    | Yes      | NULL           | -                            |
| `notes`           | TEXT            | Yes      | NULL           | -                            |
| `feedback`        | TEXT            | Yes      | NULL           | -                            |
| `graded_by`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id       |
| `graded_at`       | TIMESTAMP       | Yes      | NULL           | -                            |
| `submission_path` | VARCHAR(500)    | Yes      | NULL           | -                            |
| `submission_text` | TEXT            | Yes      | NULL           | -                            |
| `submitted_at`    | TIMESTAMP       | Yes      | NULL           | -                            |
| `is_late`         | TINYINT(1)      | No       | 0              | -                            |
| `is_locked`       | TINYINT(1)      | No       | 0              | -                            |
| `locked_at`       | TIMESTAMP       | Yes      | NULL           | -                            |
| `locked_by`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id       |
| `created_at`      | TIMESTAMP       | Yes      | NULL           | -                            |
| `updated_at`      | TIMESTAMP       | Yes      | NULL           | -                            |

**Unique Constraints:**

- `student_id, assessment_id`

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `assessment_id` → [`assessments`](#assessments) (Many-to-One, ON DELETE CASCADE)
- `graded_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)
- `enrollment_id` → [`enrollments`](#enrollments) (Many-to-One, ON DELETE SET NULL)

---

#### grade_appeals

Student grade appeal requests.

| Column               | Type            | Nullable | Default        | Constraints                       |
| -------------------- | --------------- | -------- | -------------- | --------------------------------- |
| `id`                 | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                       |
| `student_id`         | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id            |
| `student_grade_id`   | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → student_grades.id   |
| `reason`             | TEXT            | No       | -              | -                                 |
| `status`             | ENUM            | No       | 'pending'      | 'pending', 'approved', 'rejected' |
| `reviewed_by`        | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id            |
| `reviewed_at`        | TIMESTAMP       | Yes      | NULL           | -                                 |
| `review_notes`       | TEXT            | Yes      | NULL           | -                                 |
| `adjusted_grade`     | DECIMAL(5,2)    | Yes      | NULL           | -                                 |
| `escalated_to_admin` | TINYINT(1)      | No       | 0              | -                                 |
| `admin_review_notes` | TEXT            | Yes      | NULL           | -                                 |
| `created_at`         | TIMESTAMP       | Yes      | NULL           | -                                 |
| `updated_at`         | TIMESTAMP       | Yes      | NULL           | -                                 |
| `deleted_at`         | TIMESTAMP       | Yes      | NULL           | -                                 |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `student_grade_id` → [`student_grades`](#student_grades) (Many-to-One, ON DELETE CASCADE)
- `reviewed_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### academic_standings

Academic standing definitions (GPA thresholds).

| Column        | Type            | Nullable | Default        | Constraints |
| ------------- | --------------- | -------- | -------------- | ----------- |
| `id`          | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY |
| `name`        | VARCHAR(100)    | No       | -              | -           |
| `name_ar`     | VARCHAR(100)    | Yes      | NULL           | -           |
| `code`        | VARCHAR(50)     | No       | -              | UNIQUE      |
| `gpa_min`     | DECIMAL(3,2)    | No       | -              | -           |
| `gpa_max`     | DECIMAL(3,2)    | No       | -              | -           |
| `description` | TEXT            | Yes      | NULL           | -           |
| `is_active`   | TINYINT(1)      | No       | 1              | -           |
| `created_at`  | TIMESTAMP       | Yes      | NULL           | -           |
| `updated_at`  | TIMESTAMP       | Yes      | NULL           | -           |

---

### Financial

#### fees

Fee type definitions.

| Column          | Type            | Nullable | Default        | Constraints                          |
| --------------- | --------------- | -------- | -------------- | ------------------------------------ |
| `id`            | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                          |
| `semester_id`   | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → semesters.id           |
| `name`          | VARCHAR(255)    | No       | -              | -                                    |
| `name_ar`       | VARCHAR(255)    | Yes      | NULL           | -                                    |
| `fee_type`      | VARCHAR(50)     | No       | -              | -                                    |
| `amount`        | DECIMAL(10,2)   | No       | -              | -                                    |
| `target`        | ENUM            | No       | 'student'      | 'all', 'student', 'teacher', 'staff' |
| `major_id`      | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → majors.id              |
| `academic_year` | YEAR            | No       | -              | -                                    |
| `due_date`      | DATE            | Yes      | NULL           | -                                    |
| `is_active`     | TINYINT(1)      | No       | 1              | -                                    |
| `description`   | TEXT            | Yes      | NULL           | -                                    |
| `created_at`    | TIMESTAMP       | Yes      | NULL           | -                                    |
| `updated_at`    | TIMESTAMP       | Yes      | NULL           | -                                    |

**Relationships:**

- `semester_id` → [`semesters`](#semesters) (Many-to-One, ON DELETE SET NULL)
- `major_id` → [`majors`](#majors) (Many-to-One, ON DELETE SET NULL)

---

#### student_fees

Student-specific fee assignments.

| Column        | Type            | Nullable | Default        | Constraints                 |
| ------------- | --------------- | -------- | -------------- | --------------------------- |
| `id`          | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                 |
| `student_id`  | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id      |
| `fee_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → fees.id       |
| `amount`      | DECIMAL(10,2)   | No       | -              | -                           |
| `paid_amount` | DECIMAL(10,2)   | No       | 0              | -                           |
| `status`      | ENUM            | No       | 'unpaid'       | 'unpaid', 'partial', 'paid' |
| `due_date`    | DATE            | Yes      | NULL           | -                           |
| `created_at`  | TIMESTAMP       | Yes      | NULL           | -                           |
| `updated_at`  | TIMESTAMP       | Yes      | NULL           | -                           |

**Unique Constraints:**

- `student_id, fee_id`

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `fee_id` → [`fees`](#fees) (Many-to-One, ON DELETE CASCADE)

---

#### payments

Payment records.

| Column                   | Type            | Nullable | Default        | Constraints                                  |
| ------------------------ | --------------- | -------- | -------------- | -------------------------------------------- |
| `id`                     | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                  |
| `student_id`             | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                       |
| `student_fee_id`         | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → student_fees.id                |
| `transaction_id`         | VARCHAR(100)    | No       | -              | UNIQUE                                       |
| `amount`                 | DECIMAL(10,2)   | No       | -              | -                                            |
| `payment_method`         | VARCHAR(50)     | No       | -              | -                                            |
| `payment_gateway`        | VARCHAR(50)     | Yes      | NULL           | -                                            |
| `gateway_transaction_id` | VARCHAR(100)    | Yes      | NULL           | -                                            |
| `status`                 | ENUM            | No       | 'pending'      | 'pending', 'completed', 'failed', 'refunded' |
| `approved_by`            | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id                       |
| `approved_at`            | TIMESTAMP       | Yes      | NULL           | -                                            |
| `notes`                  | TEXT            | Yes      | NULL           | -                                            |
| `receipt_path`           | VARCHAR(500)    | Yes      | NULL           | -                                            |
| `created_at`             | TIMESTAMP       | Yes      | NULL           | -                                            |
| `updated_at`             | TIMESTAMP       | Yes      | NULL           | -                                            |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `student_fee_id` → [`student_fees`](#student_fees) (Many-to-One, ON DELETE SET NULL)
- `approved_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

### Communication

#### announcements

System announcements.

| Column                 | Type            | Nullable | Default        | Constraints                                            |
| ---------------------- | --------------- | -------- | -------------- | ------------------------------------------------------ |
| `id`                   | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                            |
| `user_id`              | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                                 |
| `title`                | VARCHAR(255)    | No       | -              | -                                                      |
| `title_ar`             | VARCHAR(255)    | Yes      | NULL           | -                                                      |
| `content`              | TEXT            | No       | -              | -                                                      |
| `content_ar`           | TEXT            | Yes      | NULL           | -                                                      |
| `target_type`          | ENUM            | No       | 'global'       | 'global', 'faculty', 'department', 'course', 'section' |
| `target_faculty_id`    | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → faculties.id                             |
| `target_department_id` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → departments.id                           |
| `target_course_id`     | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → courses.id                               |
| `target_offering_id`   | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → course_sections.id                       |
| `is_published`         | TINYINT(1)      | No       | 0              | -                                                      |
| `published_at`         | TIMESTAMP       | Yes      | NULL           | -                                                      |
| `created_at`           | TIMESTAMP       | Yes      | NULL           | -                                                      |
| `updated_at`           | TIMESTAMP       | Yes      | NULL           | -                                                      |

**Relationships:**

- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### messages

User-to-user messages.

| Column        | Type            | Nullable | Default        | Constraints            |
| ------------- | --------------- | -------- | -------------- | ---------------------- |
| `id`          | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY            |
| `sender_id`   | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id |
| `receiver_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id |
| `subject`     | VARCHAR(255)    | Yes      | NULL           | -                      |
| `content`     | TEXT            | No       | -              | -                      |
| `is_read`     | TINYINT(1)      | No       | 0              | -                      |
| `read_at`     | TIMESTAMP       | Yes      | NULL           | -                      |
| `created_at`  | TIMESTAMP       | Yes      | NULL           | -                      |
| `updated_at`  | TIMESTAMP       | Yes      | NULL           | -                      |

**Relationships:**

- `sender_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `receiver_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### notifications

In-app notifications.

| Column       | Type            | Nullable | Default        | Constraints            |
| ------------ | --------------- | -------- | -------------- | ---------------------- |
| `id`         | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY            |
| `user_id`    | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id |
| `type`       | VARCHAR(100)    | No       | -              | -                      |
| `title`      | VARCHAR(255)    | No       | -              | -                      |
| `content`    | TEXT            | No       | -              | -                      |
| `link`       | VARCHAR(500)    | Yes      | NULL           | -                      |
| `is_read`    | TINYINT(1)      | No       | 0              | -                      |
| `read_at`    | TIMESTAMP       | Yes      | NULL           | -                      |
| `data`       | JSON            | Yes      | NULL           | -                      |
| `created_at` | TIMESTAMP       | Yes      | NULL           | -                      |
| `updated_at` | TIMESTAMP       | Yes      | NULL           | -                      |

**Relationships:**

- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### medical_records

Student medical information.

| Column                       | Type            | Nullable | Constraints            |
| ---------------------------- | --------------- | -------- | ---------------------- |
| `id`                         | BIGINT UNSIGNED | No       | PRIMARY KEY            |
| `student_id`                 | BIGINT UNSIGNED | No       | FOREIGN KEY → users.id |
| `blood_type`                 | VARCHAR(10)     | Yes      | -                      |
| `allergies`                  | TEXT            | Yes      | -                      |
| `medical_conditions`         | TEXT            | Yes      | -                      |
| `current_medications`        | TEXT            | Yes      | -                      |
| `emergency_contact_name`     | VARCHAR(255)    | Yes      | -                      |
| `emergency_contact_phone`    | VARCHAR(50)     | Yes      | -                      |
| `emergency_contact_relation` | VARCHAR(100)    | Yes      | -                      |
| `notes`                      | TEXT            | Yes      | -                      |
| `created_at`                 | TIMESTAMP       | Yes      | -                      |
| `updated_at`                 | TIMESTAMP       | Yes      | -                      |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### medical_leaves

Student medical leave requests.

| Column            | Type            | Nullable | Default        | Constraints                       |
| ----------------- | --------------- | -------- | -------------- | --------------------------------- |
| `id`              | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                       |
| `student_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id            |
| `start_date`      | DATE            | No       | -              | -                                 |
| `end_date`        | DATE            | No       | -              | -                                 |
| `reason`          | TEXT            | No       | -              | -                                 |
| `attachment_path` | VARCHAR(500)    | Yes      | NULL           | -                                 |
| `status`          | ENUM            | No       | 'pending'      | 'pending', 'approved', 'rejected' |
| `reviewed_by`     | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id            |
| `reviewed_at`     | TIMESTAMP       | Yes      | NULL           | -                                 |
| `review_notes`    | TEXT            | Yes      | NULL           | -                                 |
| `created_at`      | TIMESTAMP       | Yes      | NULL           | -                                 |
| `updated_at`      | TIMESTAMP       | Yes      | NULL           | -                                 |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `reviewed_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### excused_absences

Excused absence requests.

| Column              | Type            | Nullable | Default        | Constraints                       |
| ------------------- | --------------- | -------- | -------------- | --------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                       |
| `student_id`        | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id            |
| `course_section_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id  |
| `start_date`        | DATE            | No       | -              | -                                 |
| `end_date`          | DATE            | No       | -              | -                                 |
| `reason`            | TEXT            | No       | -              | -                                 |
| `attachment_path`   | VARCHAR(500)    | Yes      | NULL           | -                                 |
| `status`            | ENUM            | No       | 'pending'      | 'pending', 'approved', 'rejected' |
| `reviewed_by`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id            |
| `reviewed_at`       | TIMESTAMP       | Yes      | NULL           | -                                 |
| `review_notes`      | TEXT            | Yes      | NULL           | -                                 |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                 |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                 |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `reviewed_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

### Productivity

#### notes

User notes.

| Column              | Type            | Nullable | Default        | Constraints                      |
| ------------------- | --------------- | -------- | -------------- | -------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                      |
| `user_id`           | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id           |
| `title`             | VARCHAR(255)    | No       | -              | -                                |
| `content`           | TEXT            | Yes      | NULL           | -                                |
| `color`             | VARCHAR(20)     | No       | '#3b82f6'      | -                                |
| `is_pinned`         | TINYINT(1)      | No       | 0              | -                                |
| `course_section_id` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → course_sections.id |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                |

**Relationships:**

- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE SET NULL)

---

#### calendar_events

User calendar events.

| Column              | Type            | Nullable | Default        | Constraints                                                   |
| ------------------- | --------------- | -------- | -------------- | ------------------------------------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                                   |
| `user_id`           | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id                                        |
| `title`             | VARCHAR(255)    | No       | -              | -                                                             |
| `description`       | TEXT            | Yes      | NULL           | -                                                             |
| `start_date`        | DATE            | No       | -              | -                                                             |
| `start_time`        | TIME            | Yes      | NULL           | -                                                             |
| `end_date`          | DATE            | Yes      | NULL           | -                                                             |
| `end_time`          | TIME            | Yes      | NULL           | -                                                             |
| `is_all_day`        | TINYINT(1)      | No       | 0              | -                                                             |
| `color`             | VARCHAR(20)     | No       | '#3b82f6'      | -                                                             |
| `location`          | VARCHAR(255)    | Yes      | NULL           | -                                                             |
| `event_type`        | ENUM            | No       | 'personal'     | 'personal', 'exam', 'assignment', 'class', 'meeting', 'other' |
| `course_section_id` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → course_sections.id                              |
| `reminder_enabled`  | TINYINT(1)      | No       | 0              | -                                                             |
| `reminder_minutes`  | INT             | No       | 30             | -                                                             |
| `is_recurring`      | TINYINT(1)      | No       | 0              | -                                                             |
| `recurrence_rule`   | VARCHAR(500)    | Yes      | NULL           | -                                                             |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                                             |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                                             |

**Relationships:**

- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE SET NULL)

---

#### tasks

User tasks/todos.

| Column              | Type            | Nullable | Default        | Constraints                      |
| ------------------- | --------------- | -------- | -------------- | -------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                      |
| `user_id`           | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id           |
| `title`             | VARCHAR(255)    | No       | -              | -                                |
| `description`       | TEXT            | Yes      | NULL           | -                                |
| `is_completed`      | TINYINT(1)      | No       | 0              | -                                |
| `completed_at`      | TIMESTAMP       | Yes      | NULL           | -                                |
| `priority`          | INT             | No       | 2              | 1=low, 2=medium, 3=high          |
| `due_date`          | DATE            | Yes      | NULL           | -                                |
| `due_time`          | TIME            | Yes      | NULL           | -                                |
| `course_section_id` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → course_sections.id |
| `reminder_id`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → calendar_events.id |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                |

**Relationships:**

- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE SET NULL)
- `reminder_id` → [`calendar_events`](#calendar_events) (Many-to-One, ON DELETE SET NULL)

---

#### activity_logs

System activity logging.

| Column        | Type            | Nullable | Constraints            |
| ------------- | --------------- | -------- | ---------------------- |
| `id`          | BIGINT UNSIGNED | No       | PRIMARY KEY            |
| `user_id`     | BIGINT UNSIGNED | Yes      | FOREIGN KEY → users.id |
| `action`      | VARCHAR(100)    | No       | -                      |
| `entity_type` | VARCHAR(100)    | Yes      | -                      |
| `entity_id`   | BIGINT UNSIGNED | Yes      | -                      |
| `description` | TEXT            | Yes      | -                      |
| `old_values`  | JSON            | Yes      | -                      |
| `new_values`  | JSON            | Yes      | -                      |
| `ip_address`  | VARCHAR(45)     | Yes      | -                      |
| `user_agent`  | TEXT            | Yes      | -                      |
| `created_at`  | TIMESTAMP       | Yes      | -                      |

---

#### reminders

Reminders for events.

| Column       | Type            | Nullable | Constraints            |
| ------------ | --------------- | -------- | ---------------------- |
| `id`         | BIGINT UNSIGNED | No       | PRIMARY KEY            |
| `user_id`    | BIGINT UNSIGNED | No       | FOREIGN KEY → users.id |
| `title`      | VARCHAR(255)    | No       | -                      |
| `remind_at`  | DATETIME        | No       | -                      |
| `is_read`    | TINYINT(1)      | No       | DEFAULT 0              |
| `link`       | VARCHAR(500)    | Yes      | -                      |
| `created_at` | TIMESTAMP       | Yes      | -                      |

---

### Discussions

#### discussion_forums

Course-level discussion forums.

| Column              | Type            | Nullable | Default        | Constraints                      |
| ------------------- | --------------- | -------- | -------------- | -------------------------------- |
| `id`                | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                      |
| `course_section_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id |
| `title`             | VARCHAR(255)    | No       | -              | -                                |
| `description`       | TEXT            | Yes      | NULL           | -                                |
| `is_locked`         | TINYINT(1)      | No       | 0              | -                                |
| `is_pinned`         | TINYINT(1)      | No       | 0              | -                                |
| `created_by`        | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id           |
| `created_at`        | TIMESTAMP       | Yes      | NULL           | -                                |
| `updated_at`        | TIMESTAMP       | Yes      | NULL           | -                                |
| `deleted_at`        | TIMESTAMP       | Yes      | NULL           | -                                |

**Relationships:**

- `course_section_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)
- `created_by` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)

---

#### discussion_topics

Discussion topics within forums.

| Column            | Type            | Nullable | Default        | Constraints                        |
| ----------------- | --------------- | -------- | -------------- | ---------------------------------- |
| `id`              | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                        |
| `forum_id`        | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → discussion_forums.id |
| `user_id`         | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id             |
| `title`           | VARCHAR(255)    | No       | -              | -                                  |
| `content`         | TEXT            | No       | -              | -                                  |
| `is_locked`       | TINYINT(1)      | No       | 0              | -                                  |
| `is_pinned`       | TINYINT(1)      | No       | 0              | -                                  |
| `is_announcement` | TINYINT(1)      | No       | 0              | -                                  |
| `views_count`     | INT             | No       | 0              | -                                  |
| `last_reply_at`   | TIMESTAMP       | Yes      | NULL           | -                                  |
| `last_reply_by`   | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id             |
| `created_at`      | TIMESTAMP       | Yes      | NULL           | -                                  |
| `updated_at`      | TIMESTAMP       | Yes      | NULL           | -                                  |
| `deleted_at`      | TIMESTAMP       | Yes      | NULL           | -                                  |

**Relationships:**

- `forum_id` → [`discussion_forums`](#discussion_forums) (Many-to-One, ON DELETE CASCADE)
- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `last_reply_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### discussion_replies

Replies to discussion topics.

| Column           | Type            | Nullable | Default        | Constraints                         |
| ---------------- | --------------- | -------- | -------------- | ----------------------------------- |
| `id`             | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                         |
| `topic_id`       | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → discussion_topics.id  |
| `user_id`        | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id              |
| `parent_id`      | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → discussion_replies.id |
| `content`        | TEXT            | No       | -              | -                                   |
| `is_best_answer` | TINYINT(1)      | No       | 0              | -                                   |
| `marked_best_by` | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id              |
| `created_at`     | TIMESTAMP       | Yes      | NULL           | -                                   |
| `updated_at`     | TIMESTAMP       | Yes      | NULL           | -                                   |
| `deleted_at`     | TIMESTAMP       | Yes      | NULL           | -                                   |

**Relationships:**

- `topic_id` → [`discussion_topics`](#discussion_topics) (Many-to-One, ON DELETE CASCADE)
- `user_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `parent_id` → [`discussion_replies`](#discussion_replies) (Many-to-One, ON DELETE CASCADE)
- `marked_best_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

### Assessments & Quizzes

#### questions

Quiz questions.

| Column           | Type            | Nullable | Default        | Constraints                                     |
| ---------------- | --------------- | -------- | -------------- | ----------------------------------------------- |
| `id`             | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                     |
| `assessment_id`  | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → assessments.id                    |
| `question_text`  | TEXT            | No       | -              | -                                               |
| `question_type`  | VARCHAR(50)     | No       | -              | 'multiple_choice', 'true_false', 'short_answer' |
| `options`        | JSON            | Yes      | NULL           | -                                               |
| `correct_answer` | TEXT            | Yes      | NULL           | -                                               |
| `points`         | INT             | No       | 1              | -                                               |
| `explanation`    | TEXT            | Yes      | NULL           | -                                               |
| `order`          | INT             | No       | 0              | -                                               |
| `created_at`     | TIMESTAMP       | Yes      | NULL           | -                                               |
| `updated_at`     | TIMESTAMP       | Yes      | NULL           | -                                               |

**Relationships:**

- `assessment_id` → [`assessments`](#assessments) (Many-to-One, ON DELETE CASCADE)

---

#### student_answers

Student answers to quiz questions.

| Column             | Type            | Nullable | Default        | Constraints                     |
| ------------------ | --------------- | -------- | -------------- | ------------------------------- |
| `id`               | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                     |
| `student_grade_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → student_grades.id |
| `question_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → questions.id      |
| `attempt_id`       | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → quiz_attempts.id  |
| `answer`           | TEXT            | Yes      | NULL           | -                               |
| `is_correct`       | TINYINT(1)      | Yes      | NULL           | -                               |
| `points_earned`    | INT             | Yes      | NULL           | -                               |
| `created_at`       | TIMESTAMP       | Yes      | NULL           | -                               |
| `updated_at`       | TIMESTAMP       | Yes      | NULL           | -                               |

**Relationships:**

- `student_grade_id` → [`student_grades`](#student_grades) (Many-to-One, ON DELETE CASCADE)
- `question_id` → [`questions`](#questions) (Many-to-One, ON DELETE CASCADE)

---

#### quiz_attempts

Quiz attempt tracking.

| Column               | Type            | Nullable | Default        | Constraints                  |
| -------------------- | --------------- | -------- | -------------- | ---------------------------- |
| `id`                 | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                  |
| `student_id`         | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id       |
| `assessment_id`      | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → assessments.id |
| `attempt_number`     | INT             | No       | 1              | -                            |
| `started_at`         | TIMESTAMP       | No       | -              | -                            |
| `submitted_at`       | TIMESTAMP       | Yes      | NULL           | -                            |
| `time_spent_seconds` | INT             | Yes      | NULL           | -                            |
| `score`              | DECIMAL(5,2)    | Yes      | NULL           | -                            |
| `is_completed`       | TINYINT(1)      | No       | 0              | -                            |
| `created_at`         | TIMESTAMP       | Yes      | NULL           | -                            |
| `updated_at`         | TIMESTAMP       | Yes      | NULL           | -                            |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `assessment_id` → [`assessments`](#assessments) (Many-to-One, ON DELETE CASCADE)

---

### Accommodations

#### accommodation_types

Types of student accommodations.

| Column                   | Type            | Nullable | Default        | Constraints                                    |
| ------------------------ | --------------- | -------- | -------------- | ---------------------------------------------- |
| `id`                     | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                                    |
| `name`                   | VARCHAR(255)    | No       | -              | -                                              |
| `code`                   | VARCHAR(50)     | No       | -              | UNIQUE                                         |
| `description`            | TEXT            | Yes      | NULL           | -                                              |
| `category`               | VARCHAR(100)    | No       | -              | 'timing', 'format', 'environment', 'materials' |
| `default_settings`       | JSON            | Yes      | NULL           | -                                              |
| `requires_documentation` | TINYINT(1)      | No       | 0              | -                                              |
| `is_active`              | TINYINT(1)      | No       | 1              | -                                              |
| `created_at`             | TIMESTAMP       | Yes      | NULL           | -                                              |
| `updated_at`             | TIMESTAMP       | Yes      | NULL           | -                                              |
| `deleted_at`             | TIMESTAMP       | Yes      | NULL           | -                                              |

---

#### student_accommodations

Student-specific accommodations.

| Column                  | Type            | Nullable | Default        | Constraints                          |
| ----------------------- | --------------- | -------- | -------------- | ------------------------------------ |
| `id`                    | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                          |
| `student_id`            | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id               |
| `accommodation_type_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → accommodation_types.id |
| `notes`                 | TEXT            | Yes      | NULL           | -                                    |
| `custom_settings`       | JSON            | Yes      | NULL           | -                                    |
| `start_date`            | DATE            | Yes      | NULL           | -                                    |
| `end_date`              | DATE            | Yes      | NULL           | -                                    |
| `status`                | VARCHAR(50)     | No       | 'active'       | 'active', 'expired', 'suspended'     |
| `approved_by`           | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id               |
| `approved_at`           | TIMESTAMP       | Yes      | NULL           | -                                    |
| `documentation_path`    | VARCHAR(500)    | Yes      | NULL           | -                                    |
| `created_at`            | TIMESTAMP       | Yes      | NULL           | -                                    |
| `updated_at`            | TIMESTAMP       | Yes      | NULL           | -                                    |
| `deleted_at`            | TIMESTAMP       | Yes      | NULL           | -                                    |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `accommodation_type_id` → [`accommodation_types`](#accommodation_types) (Many-to-One, ON DELETE CASCADE)
- `approved_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

#### quiz_accommodations

Quiz-specific accommodations.

| Column                     | Type            | Nullable | Default        | Constraints                             |
| -------------------------- | --------------- | -------- | -------------- | --------------------------------------- |
| `id`                       | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                             |
| `student_accommodation_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → student_accommodations.id |
| `assessment_id`            | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → assessments.id            |
| `extended_time_minutes`    | INT             | Yes      | NULL           | -                                       |
| `extended_time_percentage` | DECIMAL(5,2)    | Yes      | NULL           | -                                       |
| `additional_attempts`      | INT             | No       | 0              | -                                       |
| `allow_breaks`             | TINYINT(1)      | No       | 0              | -                                       |
| `special_instructions`     | TEXT            | Yes      | NULL           | -                                       |
| `is_applied`               | TINYINT(1)      | No       | 0              | -                                       |
| `applied_at`               | TIMESTAMP       | Yes      | NULL           | -                                       |
| `applied_by`               | BIGINT UNSIGNED | Yes      | NULL           | FOREIGN KEY → users.id                  |
| `created_at`               | TIMESTAMP       | Yes      | NULL           | -                                       |
| `updated_at`               | TIMESTAMP       | Yes      | NULL           | -                                       |

**Relationships:**

- `student_accommodation_id` → [`student_accommodations`](#student_accommodations) (Many-to-One, ON DELETE CASCADE)
- `assessment_id` → [`assessments`](#assessments) (Many-to-One, ON DELETE CASCADE)
- `applied_by` → [`users`](#users) (Many-to-One, ON DELETE SET NULL)

---

### Course Feedback

#### course_feedback

Student course feedback.

| Column               | Type            | Nullable | Default        | Constraints                      |
| -------------------- | --------------- | -------- | -------------- | -------------------------------- |
| `id`                 | BIGINT UNSIGNED | No       | AUTO_INCREMENT | PRIMARY KEY                      |
| `student_id`         | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → users.id           |
| `course_offering_id` | BIGINT UNSIGNED | No       | -              | FOREIGN KEY → course_sections.id |
| `rating`             | DECIMAL(3,2)    | No       | -              | 1-5 scale                        |
| `feedback_text`      | TEXT            | Yes      | NULL           | -                                |
| `is_anonymous`       | TINYINT(1)      | No       | 0              | -                                |
| `created_at`         | TIMESTAMP       | Yes      | NULL           | -                                |
| `updated_at`         | TIMESTAMP       | Yes      | NULL           | -                                |

**Relationships:**

- `student_id` → [`users`](#users) (Many-to-One, ON DELETE CASCADE)
- `course_offering_id` → [`course_sections`](#course_sections) (Many-to-One, ON DELETE CASCADE)

---

## 📊 Indexes

The following indexes are created for performance optimization:

| Table                    | Index Name                                    | Columns                                          | Type   |
| ------------------------ | --------------------------------------------- | ------------------------------------------------ | ------ |
| `users`                  | `users_email_unique`                          | `email`                                          | UNIQUE |
| `users`                  | `users_user_id_unique`                        | `user_id`                                        | UNIQUE |
| `enrollments`            | `enrollments_student_course_semester_unique`  | `student_id`, `course_section_id`, `semester_id` | UNIQUE |
| `enrollments`            | `enrollments_student_id_index`                | `student_id`                                     | INDEX  |
| `enrollments`            | `enrollments_course_section_id_index`         | `course_section_id`                              | INDEX  |
| `student_grades`         | `student_grades_student_assessment_unique`    | `student_id`, `assessment_id`                    | UNIQUE |
| `student_grades`         | `student_grades_student_id_index`             | `student_id`                                     | INDEX  |
| `student_grades`         | `student_grades_assessment_id_index`          | `assessment_id`                                  | INDEX  |
| `attendance_records`     | `attendance_student_course_date_unique`       | `student_id`, `course_section_id`, `date`        | UNIQUE |
| `activity_logs`          | `activity_logs_entity_index`                  | `entity_type`, `entity_id`                       | INDEX  |
| `activity_logs`          | `activity_logs_user_id_index`                 | `user_id`                                        | INDEX  |
| `discussion_forums`      | `discussion_forums_course_pinned_index`       | `course_section_id`, `is_pinned`                 | INDEX  |
| `discussion_topics`      | `discussion_topics_forum_pinned_index`        | `forum_id`, `is_pinned`                          | INDEX  |
| `discussion_topics`      | `discussion_topics_forum_reply_index`         | `forum_id`, `last_reply_at`                      | INDEX  |
| `discussion_replies`     | `discussion_replies_topic_created_index`      | `topic_id`, `created_at`                         | INDEX  |
| `student_accommodations` | `student_accommodations_student_status_index` | `student_id`, `status`                           | INDEX  |
| `student_accommodations` | `student_accommodations_type_status_index`    | `accommodation_type_id`, `status`                | INDEX  |

---

## 🔗 Relationships Summary

### One-to-One Relationships

| Parent Table | Child Table       | Description              |
| ------------ | ----------------- | ------------------------ |
| `users`      | `user_profiles`   | User profile information |
| `users`      | `user_settings`   | User preferences         |
| `users`      | `medical_records` | Student medical info     |

### One-to-Many Relationships

| Parent Table      | Child Table        | Description                               |
| ----------------- | ------------------ | ----------------------------------------- |
| `academic_years`  | `semesters`        | Academic year has multiple semesters      |
| `faculties`       | `departments`      | Faculty has multiple departments          |
| `departments`     | `majors`           | Department has multiple majors            |
| `departments`     | `courses`          | Department offers multiple courses        |
| `courses`         | `course_sections`  | Course has multiple sections per semester |
| `course_sections` | `course_materials` | Section has multiple materials            |
| `course_sections` | `enrollments`      | Section has multiple enrollments          |
| `course_sections` | `assessments`      | Section has multiple assessments          |
| `assessments`     | `questions`        | Assessment has multiple questions         |
| `assessments`     | `student_grades`   | Assessment has multiple student grades    |

### Many-to-Many Relationships

| Table 1           | Table 2            | Pivot Table       |
| ----------------- | ------------------ | ----------------- |
| `course_sections` | `users` (teachers) | `course_teachers` |
| `courses`         | `majors`           | `course_major`    |

---

## 🗄️ Database Setup

### Initial Setup

```bash
# Clone the repository
git clone https://github.com/RamiAlAjo/noor-alhuda-lms.git
cd noor-alhuda-lms

# Install dependencies
composer install
npm install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# For SQLite:
touch database/database.sqlite
DB_CONNECTION=sqlite

# For MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations
php artisan migrate
```

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Fresh migrate (drops all tables and re-migrates)
php artisan migrate:fresh

# Fresh migrate with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Show migration status
php artisan migrate:status
```

---

## 🌱 Database Seeding

### Running Seeders

```bash
# Seed the database
php artisan db:seed

# Fresh migrate and seed
php artisan migrate:fresh --seed
```

### Available Seeders

| Seeder                 | Description                       |
| ---------------------- | --------------------------------- |
| `DatabaseSeeder`       | Main seeder that calls all others |
| `UserSeeder`           | Creates default users with roles  |
| `AcademicSeeder`       | Creates academic structure        |
| `CourseSeeder`         | Creates sample courses            |
| `AssessmentTypeSeeder` | Creates assessment types          |

---

## 💾 Backup & Restore

### Manual MySQL Backup

```bash
# Backup entire database
mysqldump -u username -p database_name > backup.sql

# Backup with compression
mysqldump -u username -p database_name | gzip > backup_$(date +%Y%m%d).sql.gz
```

### MySQL Restore

```bash
# Restore from backup
mysql -u username -p database_name < backup.sql
```

### Best Practices

1. **Regular Backups**: Schedule daily automated backups
2. **Offsite Storage**: Store backups in cloud storage
3. **Backup Testing**: Regularly test restore procedures
4. **Version Control**: Keep backup files organized with timestamps

---

## ⚡ Performance Considerations

### Database Optimization

- Primary keys are auto-indexed
- Foreign keys have corresponding indexes for JOIN performance
- Use `EXPLAIN` to analyze query performance

### Caching

```bash
# Cache configuration
CACHE_STORE=database  # For development
CACHE_STORE=redis     # For production
```

### Recommended Production Settings

| Setting            | Recommended Value |
| ------------------ | ----------------- |
| `DB_CONNECTION`    | mysql             |
| `CACHE_STORE`      | redis             |
| `QUEUE_CONNECTION` | redis             |
| `SESSION_DRIVER`   | redis             |

---

## 📋 Migration Reference

| Migration Date    | File                               | Description                            |
| ----------------- | ---------------------------------- | -------------------------------------- |
| 0001_01_01_000000 | create_users_table.php             | Core users, sessions, password reset   |
| 2026_02_16_170000 | create_user_profiles_table.php     | User profiles and settings             |
| 2026_02_16_170100 | create_academic_tables.php         | Academic structure                     |
| 2026_02_16_170200 | create_courses_tables.php          | Courses and sections                   |
| 2026_02_16_170300 | create_enrollment_tables.php       | Enrollments, fees, payments            |
| 2026_02_16_170400 | create_academic_records_tables.php | Grades, attendance, appeals            |
| 2026_02_16_170500 | create_communication_tables.php    | Announcements, messages, notifications |
| 2026_02_16_170600 | create_productivity_tables.php     | Notes, calendar, tasks                 |
| 2026_02_21_120000 | add_performance_indexes.php        | Performance indexes                    |
| 2026_02_21_030100 | create_discussion_forums_table.php | Discussion forums                      |
| 2026_02_21_030700 | create_accommodations_table.php    | Student accommodations                 |
| 2026_02_25_000000 | fix_missing_database_columns.php   | Fix missing database columns           |
| 2026_05_07_162900 | update_course_materials_enum...php | Update course materials enum           |
| 2026_05_07_180229 | add_base_theme_and_appearance...php| Add theme settings columns            |

---

_Document generated for Noor Alhuda LMS_
