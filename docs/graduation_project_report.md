# Noor Alhuda Learning Management System with AI-Powered Predictive Analytics

## ABSTRACT

This graduation project describes the development of a web-based Learning Management System (LMS) designed to support the administrative and academic needs of educational institutions. The system provides role-based functionality for administrators, teachers, and students, incorporating basic predictive analytics to assist with academic decision-making. Built using the Laravel framework, the system integrates with external machine learning APIs to generate predictions about student performance and course capacity utilization.

The project demonstrates the practical application of web development technologies and machine learning integration in an educational context. Key features include user management, course administration, assessment handling, and financial processing. The predictive functionality serves as a supplementary tool for administrators, providing data-driven insights while maintaining human oversight of decision-making processes.

## TABLE OF CONTENTS

1. [INTRODUCTION](#1-introduction)
   1.1 [Background](#11-background)
   1.2 [Problem Statement](#12-problem-statement)
   1.3 [Project Objectives](#13-project-objectives)
   1.4 [Scope and Limitations](#14-scope-and-limitations)

2. [LITERATURE REVIEW](#2-literature-review)
   2.1 [Learning Management Systems](#21-learning-management-systems)
   2.2 [Web Application Frameworks](#22-web-application-frameworks)
   2.3 [Predictive Analytics in Education](#23-predictive-analytics-in-education)
   2.4 [Machine Learning Integration](#24-machine-learning-integration)

3. [METHODOLOGY](#3-methodology)
   3.1 [Development Approach](#31-development-approach)
   3.2 [Tools and Technologies](#32-tools-and-technologies)
   3.3 [Project Timeline](#33-project-timeline)
   3.4 [Risk Assessment](#34-risk-assessment)

4. [SYSTEM ANALYSIS](#4-system-analysis)
   4.1 [Requirements Gathering](#41-requirements-gathering)
   4.2 [Functional Requirements](#42-functional-requirements)
   4.3 [Non-Functional Requirements](#43-non-functional-requirements)
   4.4 [User Roles and Permissions](#44-user-roles-and-permissions)

5. [SYSTEM DESIGN](#5-system-design)
   5.1 [System Architecture](#51-system-architecture)
   5.2 [Database Design](#52-database-design)
   5.3 [User Interface Design](#53-user-interface-design)
   5.4 [Predictive Analytics Module](#54-predictive-analytics-module)

6. [IMPLEMENTATION](#6-implementation)
   6.1 [Development Environment](#61-development-environment)
   6.2 [Core Module Development](#62-core-module-development)
   6.3 [AI Integration](#63-ai-integration)
   6.4 [Security Implementation](#64-security-implementation)

7. [TESTING](#7-testing)
   7.1 [Testing Strategy](#71-testing-strategy)
   7.2 [Unit Testing](#72-unit-testing)
   7.3 [Integration Testing](#73-integration-testing)
   7.4 [User Acceptance Testing](#74-user-acceptance-testing)

8. [RESULTS AND DISCUSSION](#8-results-and-discussion)
   8.1 [System Performance](#81-system-performance)
   8.2 [Predictive Analytics Evaluation](#82-predictive-analytics-evaluation)
   8.3 [User Feedback](#83-user-feedback)
   8.4 [Project Achievements](#84-project-achievements)
   8.5 [Limitations](#85-limitations)

9. [CONCLUSION](#9-conclusion)
   9.1 [Project Summary](#91-project-summary)
   9.2 [Lessons Learned](#92-lessons-learned)
   9.3 [Future Work](#93-future-work)

## REFERENCES

## APPENDICES

A. [System Requirements Specification](#appendix-a-system-requirements-specification)
B. [Database Schema](#appendix-b-database-schema)
C. [API Integration Details](#appendix-c-api-integration-details)
D. [Test Cases](#appendix-d-test-cases)
E. [User Manual](#appendix-e-user-manual)

## 1. INTRODUCTION

### 1.1 Background

Learning Management Systems (LMS) have become standard tools in educational institutions, providing centralized platforms for managing course delivery, student enrollment, and academic assessment. These systems help institutions organize their educational processes more efficiently compared to traditional paper-based or fragmented digital approaches. The Noor Alhuda LMS project was developed to address the specific needs of educational institutions requiring a comprehensive yet straightforward system for managing academic operations.

The system incorporates basic predictive analytics functionality to assist administrators in making data-informed decisions about student performance and course capacity. This feature represents an enhancement to the core LMS functionality rather than a standalone artificial intelligence platform.

### 1.2 Problem Statement

Educational institutions often struggle with inefficient administrative processes that rely heavily on manual coordination and limited data analysis capabilities. Common challenges include:

- Difficulty tracking student enrollment across multiple courses and semesters
- Limited ability to predict course demand and optimize class sizes
- Manual processes for grade entry and assessment management
- Challenges in coordinating communication between administrators, teachers, and students
- Limited tools for analyzing academic performance trends

These operational challenges become more pronounced as institutions grow and need to manage increasing numbers of students and courses. The project addresses these issues by developing a web-based system that automates routine administrative tasks and provides basic analytical support.

### 1.3 Project Objectives

The main objectives of this project were to:

1. Develop a functional LMS that supports the core administrative needs of educational institutions
2. Implement role-based access control for administrators, teachers, and students
3. Create tools for managing courses, enrollments, and assessments
4. Integrate basic predictive analytics to support academic decision-making
5. Ensure the system meets standard web application requirements for security and usability

### 1.4 Scope and Limitations

#### 1.4.1 Project Scope

The system includes functionality for:
- User management with role-based permissions
- Course and curriculum management
- Student enrollment and registration
- Assessment creation and grading
- Basic financial tracking for fees and payments
- Predictive analytics for student performance and course capacity
- Multi-language support and responsive design

#### 1.4.2 Limitations

The project has several constraints:
- Single-institution deployment (not designed for multi-tenant use)
- Dependence on external machine learning services for predictions
- Limited offline functionality
- Scope restricted to core LMS features without advanced learning analytics
- Development focused on functional completeness rather than advanced optimization

## 2. LITERATURE REVIEW

### 2.1 Learning Management Systems

Learning Management Systems have evolved significantly since their introduction in the late 1990s. CooL (1997) described early LMS platforms as tools for delivering educational content online, while more recent systems focus on comprehensive administrative functionality. Watson (2006) categorized LMS features into content management, student tracking, and communication tools. The current project aligns with this evolution by implementing core LMS functionality while adding basic predictive capabilities.

### 2.2 Web Application Frameworks

The Laravel framework, used in this project, represents a modern PHP framework following the Model-View-Controller (MVC) architectural pattern. Otwell (2011) developed Laravel to address limitations in existing PHP frameworks by providing elegant syntax and comprehensive features. The framework's use of Eloquent ORM for database interactions and middleware for request handling makes it suitable for educational applications requiring complex data relationships and user authentication.

### 2.3 Predictive Analytics in Education

Predictive analytics in education focuses on using historical data to forecast student outcomes and optimize resource allocation. Lykourentzou et al. (2009) demonstrated that machine learning techniques could predict student performance using demographic and academic history data. However, their work also highlighted the importance of feature selection and data quality. The current project implements basic predictive functionality using external APIs rather than developing complex models internally.

### 2.4 Machine Learning Integration

Integrating machine learning into web applications requires careful consideration of API design and error handling. Sculley et al. (2015) discussed the challenges of machine learning in production systems, emphasizing the need for robust fallback mechanisms and monitoring. The project's approach of using external ML services with rule-based fallbacks addresses these concerns by ensuring system reliability when external services are unavailable.

## 3. METHODOLOGY

### 3.1 Development Approach

The project followed an iterative development approach combining elements of the waterfall and agile methodologies. Initial planning established system requirements and architectural design, followed by iterative implementation of core modules. Each iteration included coding, testing, and refinement cycles. This approach allowed for flexibility in addressing technical challenges while maintaining project milestones.

### 3.2 Tools and Technologies

The system was developed using:
- Laravel 12 framework for backend logic and MVC architecture
- MySQL database for data persistence
- Livewire for reactive user interface components
- Blade templating engine for view rendering
- Bootstrap/Tailwind CSS for responsive styling
- External ML APIs for predictive functionality

Laravel was selected for its comprehensive feature set and active community support, while the choice of external ML APIs avoided the complexity of developing custom machine learning models within the project scope.

### 3.3 Project Timeline

The project was completed over a six-month period with the following phases:
- Month 1: Requirements analysis and system design
- Months 2-4: Core module implementation and testing
- Month 5: AI integration and system integration testing
- Month 6: Final testing, documentation, and deployment preparation

### 3.4 Risk Assessment

Key risks identified included:
- Framework learning curve and potential compatibility issues
- External API dependency and service availability
- Database design complexity due to academic relationships
- User interface complexity for multiple roles

Risk mitigation strategies included incremental development, comprehensive testing, and fallback mechanisms for external service dependencies.

## 4. SYSTEM ANALYSIS

### 4.1 Requirements Gathering

Requirements were gathered through analysis of typical LMS functionality and consultation with educational administrators. The system needed to support the core administrative workflow of educational institutions while providing basic analytical capabilities. User stories were developed for each role (administrator, teacher, student) to identify specific functional needs.

### 4.2 Functional Requirements

#### 4.2.1 Administrator Requirements

Administrators must be able to:
- Create and manage user accounts with appropriate roles
- Define academic structure (faculties, departments, courses)
- Configure course offerings and assign instructors
- Monitor enrollment statistics and system usage
- Manage announcements and system-wide communications
- Process financial transactions and fee payments
- Generate basic reports on academic performance

#### 4.2.2 Teacher Requirements

Teachers must be able to:
- View assigned courses and enrolled students
- Create and manage assessments (quizzes, assignments, exams)
- Enter and modify student grades
- Record attendance for course sessions
- Communicate with students through announcements
- Access student performance data for their courses

#### 4.2.3 Student Requirements

Students must be able to:
- Browse available courses and submit enrollment requests
- View course materials and schedules
- Complete assigned assessments within time limits
- Check grades and academic progress
- Make payments for course fees
- Communicate with instructors

#### 4.2.4 Predictive Analytics Requirements

The system should provide:
- Basic predictions of student performance using historical data
- Course capacity utilization forecasts
- Simple fallback calculations when external services are unavailable

### 4.3 Non-Functional Requirements

#### 4.3.1 Performance Requirements

- System response times should remain under 2 seconds for typical operations
- Support concurrent access for up to 500 users
- Handle database queries efficiently with appropriate indexing

#### 4.3.2 Security Requirements

- Implement role-based access control preventing unauthorized data access
- Protect against common web vulnerabilities (SQL injection, XSS)
- Securely handle user authentication and session management
- Encrypt sensitive data such as payment information

#### 4.3.3 Usability Requirements

- Provide intuitive interfaces suitable for non-technical users
- Support responsive design for desktop and mobile devices
- Include multi-language support for diverse user groups
- Follow accessibility guidelines for users with disabilities

#### 4.3.4 Reliability Requirements

- Maintain system availability during normal operating conditions
- Provide fallback functionality when external services fail
- Ensure data integrity through proper validation and constraints

### 4.4 User Roles and Permissions

The system implements three primary user roles with distinct permission levels:

- **Administrator**: Full system access including user management, system configuration, and all administrative functions
- **Teacher**: Access to assigned courses, student data for those courses, and teaching-related functions
- **Student**: Access to personal academic data, enrolled courses, and student-specific functions

Permissions are enforced through Laravel's middleware and gate systems, ensuring users can only access authorized functionality.

## 5. SYSTEM DESIGN

### 5.1 System Architecture

The system follows the Model-View-Controller (MVC) architectural pattern provided by the Laravel framework. The architecture separates concerns between data handling, business logic, and user interface components.

#### System Architecture Diagram

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Browser  │◄──►│  Laravel App    │◄──►│   External APIs  │
│                 │    │                 │    │  (ML Services)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │   Database      │
                       │   (MySQL)       │
                       └─────────────────┘
```

#### 5.1.1 Presentation Layer

The user interface is built using Blade templates with Livewire components for dynamic interactions. Bootstrap and Tailwind CSS provide responsive styling and consistent visual design. The interface adapts to different screen sizes and supports multiple languages through Laravel's localization features.

#### 5.1.2 Application Layer

Controllers handle HTTP requests and coordinate between models and views. Service classes encapsulate complex business logic, such as grade calculations and enrollment processing. Middleware components manage authentication, authorization, and request filtering.

#### 5.1.3 Data Layer

Eloquent ORM models represent database entities and handle data relationships. Database migrations manage schema changes, and seeders provide initial data for development and testing. The database design supports the complex relationships inherent in academic systems.

### 5.2 Database Design

The database schema supports the hierarchical nature of academic institutions with proper relationships between entities.

#### 5.2.1 Core Tables

- **users**: User authentication and basic information
- **user_profiles**: Extended user information including contact details
- **courses**: Course definitions with academic metadata
- **course_sections**: Specific course offerings with scheduling information
- **enrollments**: Student-course relationships with status tracking
- **assessments**: Evaluation components with grading criteria
- **grades**: Student performance records

#### 5.2.2 Supporting Tables

- **departments**: Academic department organization
- **semesters**: Academic period definitions
- **fees**: Financial fee structures
- **payments**: Transaction records
- **announcements**: System communications

#### Database Relationships

```
users (1) ──── (1) user_profiles
users (1) ──── (many) enrollments
courses (1) ──── (many) course_sections
course_sections (1) ──── (many) enrollments
course_sections (1) ──── (many) assessments
enrollments (1) ──── (many) grades
departments (1) ──── (many) courses
```

#### Key Constraints

- Foreign key relationships maintain referential integrity
- Unique constraints prevent duplicate enrollments
- Check constraints validate data ranges
- Cascade operations handle related record updates

### 5.3 User Interface Design

The interface design prioritizes usability and accessibility. Navigation follows consistent patterns with clear visual hierarchy. Forms include validation feedback, and data tables support sorting and filtering. The design accommodates different user roles with role-specific dashboards and menu options.

### 5.4 Predictive Analytics Module

The predictive functionality integrates with external machine learning APIs to provide basic forecasting capabilities. The module includes:

#### 5.4.1 Data Collection

The system collects relevant academic data including enrollment history, assessment results, and attendance patterns. This data is processed to extract features suitable for predictive modeling.

#### 5.4.2 API Integration

External ML services receive processed data and return prediction results. The system handles API communication, error conditions, and result interpretation.

#### 5.4.3 Fallback Logic

When external services are unavailable, the system provides simple rule-based calculations using statistical methods and historical averages. This ensures the system remains functional even during service disruptions.

#### 5.4.4 Result Presentation

Predictions are presented to administrators through dashboard interfaces with appropriate confidence indicators. Results assist decision-making but do not replace human judgment.

#### Use Case Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Noor Alhuda LMS                          │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │ Administrator│  │   Teacher   │  │   Student   │         │
│  └─────────────┘  └─────────────┘  └─────────────┘         │
│        │                   │                   │            │
│        ├─Manage Users      ├─Create Course    ├─Enroll     │
│        ├─Configure System  ├─Enter Grades     ├─View Grades│
│        ├─View Analytics    ├─Record Attendance├─Take Quiz  │
│        └─Generate Reports  └─Manage Content   └─Make Payment│
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────┐   │
│  │              System Use Cases                      │   │
│  │  • User Authentication                              │   │
│  │  • Course Management                                │   │
│  │  • Assessment Administration                        │   │
│  │  • Payment Processing                               │   │
│  │  • Predictive Analytics                             │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## 6. IMPLEMENTATION

### 6.1 Development Environment

The system was developed using a standard web development environment. Laravel 12 provided the framework foundation with PHP 8.2 as the runtime environment. MySQL was used for data persistence during development and production. The frontend utilized Livewire for dynamic components and Bootstrap/Tailwind for styling. Development tools included Composer for dependency management and Git for version control.

#### Laravel Project Structure

```
app/
├── Http/Controllers/      # Request handling logic
│   ├── Admin/            # Administrator controllers
│   ├── Student/          # Student controllers
│   └── Teacher/          # Teacher controllers
├── Models/               # Eloquent ORM models
├── Services/             # Business logic services
└── Middleware/           # Request filtering

resources/views/          # Blade templates
routes/                   # Route definitions
database/migrations/      # Schema changes
```

#### Key Laravel Components Used

- **Eloquent ORM**: For database operations and relationships
- **Blade Templates**: For view rendering and layout management
- **Middleware**: For authentication and authorization checks
- **Service Classes**: For encapsulating business logic
- **Migrations**: For database schema management

### 6.2 Core Module Development

#### 6.2.1 User Management System

User authentication and authorization were implemented using Laravel's built-in features supplemented by role-based access control. User registration, login, and password management followed standard web application patterns. Profile management allowed users to maintain personal and academic information.

**Implementation Details:**
- Used Laravel Fortify for authentication scaffolding
- Implemented custom middleware for role-based access control
- Created Eloquent models for User and UserProfile entities
- Applied form validation using Laravel's validation rules

#### 6.2.2 Course Management

Courses were organized hierarchically with departments and sections. Administrators could create course templates, while teachers managed specific course offerings. Enrollment management handled student requests and approval workflows.

**Key Components:**
- CourseController for handling CRUD operations
- Eloquent relationships between Department, Course, and CourseSection models
- Livewire components for dynamic course enrollment forms
- Database migrations for schema management

#### 6.2.3 Assessment System

The assessment module supported different evaluation types with configurable parameters. Teachers could create quizzes, assignments, and exams with automated grading where applicable. Grade entry and modification included audit trails for academic integrity.

**Technical Implementation:**
- Assessment model with polymorphic relationships
- Grade calculation service class for weighted scoring
- Audit logging using Laravel's event system
- Form validation for assessment creation and submission

#### 6.2.4 Financial Module

Basic fee management and payment processing were implemented. The system tracked student fees, processed payments through integrated gateways, and generated financial reports.

**Payment Integration:**
- Service classes for Stripe and PayPal API communication
- Transaction logging and status tracking
- Webhook handlers for payment confirmations
- Fee calculation logic based on course enrollments

### 6.3 AI Integration

#### 6.3.1 External API Integration

The system integrated with external machine learning APIs for predictive functionality. API communication was handled through dedicated service classes with proper error handling and timeout management.

**Key Implementation Details:**
- HTTP client configuration with retry logic
- JSON payload construction for API requests
- Response parsing and error code handling
- API key management through environment variables

#### 6.3.2 Data Processing

Academic data was collected and formatted for API submission. Feature extraction focused on relevant academic indicators such as historical performance and enrollment patterns.

**Data Flow:**
1. Query database for relevant student/course data
2. Transform data into API-compatible format
3. Apply basic preprocessing (data cleaning, type conversion)
4. Send formatted data to external prediction service

#### 6.3.3 Fallback Implementation

Rule-based calculations were implemented as alternatives to external predictions. These used statistical methods and historical averages to provide basic forecasting when external services were unavailable.

**Fallback Logic:**
- Calculate average performance from historical data
- Use enrollment trends for capacity predictions
- Apply threshold-based rules for risk assessment
- Return conservative estimates when data is insufficient

### 6.4 Security Implementation

Security measures included input validation, SQL injection prevention through prepared statements, and XSS protection. Role-based middleware ensured users could only access authorized functions. Password hashing and session management followed Laravel security best practices.

## 7. TESTING

### 7.1 Testing Strategy

Testing followed a systematic approach combining automated unit tests with manual integration and user acceptance testing. PHPUnit was used for automated testing, while manual testing verified user workflows and system integration.

### 7.2 Unit Testing

Unit tests focused on individual components such as model methods, controller actions, and service classes. Tests verified correct behavior under various input conditions and error scenarios.

### 7.3 Integration Testing

Integration tests verified the interaction between different system components, including database operations, API communications, and user interface interactions.

### 7.4 User Acceptance Testing

End-users tested the system in realistic scenarios. Administrators, teachers, and students provided feedback on functionality, usability, and performance. Issues identified during testing were documented and resolved.

## 8. RESULTS AND DISCUSSION

### 8.1 System Performance

The system demonstrated acceptable performance for the intended user base. Response times for typical operations remained under 2 seconds, and the system handled concurrent users adequately. Database queries were optimized through appropriate indexing and query structure.

### 8.2 Predictive Analytics Evaluation

The predictive functionality provided basic forecasting capabilities. Predictions assisted administrators in decision-making but required human interpretation. The fallback mechanisms ensured system continuity when external services were unavailable.

### 8.3 User Feedback

User testing revealed generally positive feedback regarding core functionality. The interface was considered intuitive, and role-specific features met user needs. Some users requested additional customization options and advanced reporting features.

### 8.4 Project Achievements

The project successfully delivered a functional LMS with integrated predictive capabilities. Key achievements included:

- Complete implementation of core LMS features
- Successful integration with external services
- Robust fallback mechanisms for system reliability
- User-friendly interface across different roles
- Secure handling of academic data

### 8.5 Limitations

Several limitations were identified:

- Dependence on external machine learning services introduced reliability concerns
- Predictive accuracy was limited by data quality and external service performance
- System scope was restricted to basic functionality without advanced analytics
- Mobile application was not included in the current implementation
- Performance testing was limited to moderate user loads

## 9. CONCLUSION

### 9.1 Project Summary

This graduation project developed a web-based Learning Management System with integrated predictive analytics. The system provides essential functionality for educational institutions while demonstrating the practical integration of machine learning capabilities.

### 9.2 Lessons Learned

The project provided valuable experience in web application development, database design, and external API integration. Key lessons included the importance of thorough requirements analysis, the challenges of integrating external services, and the need for robust error handling in distributed systems.

### 9.3 Future Work

Future enhancements could include:

- Development of a mobile application companion
- Expansion of predictive capabilities with local model training
- Integration with additional external services
- Performance optimization for larger user bases

## APPENDICES

### APPENDIX A: SYSTEM REQUIREMENTS SPECIFICATION

#### Functional Requirements

- User authentication with role-based access (Admin, Teacher, Student)
- Course creation and management by administrators
- Student enrollment and registration
- Assessment creation and grading by teachers
- Basic financial tracking for fees
- Predictive analytics for student performance and course capacity

#### Non-Functional Requirements

- Response times under 2 seconds for typical operations
- Support for up to 500 concurrent users
- Secure data handling with encryption
- Responsive design for desktop and mobile devices

### APPENDIX B: DATABASE SCHEMA

The database includes the following main tables:
- users: User authentication and basic information
- courses: Course definitions and metadata
- course_sections: Course offerings with capacity limits
- enrollments: Student-course relationships
- assessments: Evaluation components
- grades: Student performance records

### APPENDIX C: API INTEGRATION DETAILS

The system integrates with external machine learning APIs for predictive functionality. API communication includes:
- Authentication using API keys
- Data transmission in JSON format
- Error handling for service unavailability
- Fallback to rule-based calculations

### APPENDIX D: TEST CASES

Sample test cases include:
- User login with valid/invalid credentials
- Course enrollment workflow
- Assessment submission and grading
- Prediction API response handling
- Fallback mechanism activation

### APPENDIX E: USER MANUAL

#### Administrator Guide
1. Log in with administrator credentials
2. Create courses and assign teachers
3. Monitor enrollment statistics
4. View prediction analytics

#### Teacher Guide
1. Access assigned courses
2. Create and manage assessments
3. Enter student grades
4. Communicate with students

#### Student Guide
1. Browse available courses
2. Submit enrollment requests
3. Complete assessments
4. View grades and progress

## REFERENCES

1. Alavi, M., & Leidner, D. E. (2001). Research commentary: Technology-mediated learning—A call for greater depth and breadth of research. *Information Systems Research*, 12(1), 1-10.

2. Blackboard Inc. (2020). *Learning management system market trends report*. Retrieved from https://www.blackboard.com

3. Brusilovsky, P., & Peylo, C. (2003). Adaptive and intelligent web-based educational systems. *International Journal of Artificial Intelligence in Education*, 13(2-4), 159-172.

4. CooL, L. (1997). Computer-mediated communication for linguistics and literacy: Technology and natural language education. *International Journal of Educational Telecommunications*, 3(3-4), 259-289.

5. Lykourentzou, I., Giannoukos, I., Nikolopoulos, V., Mpardis, G., & Loumos, V. (2009). Dropout prediction in e-Learning courses through the combination of machine learning techniques. *Computers & Education*, 53(3), 950-965.

6. Martin, R. C. (2008). *Clean code: A handbook of agile software craftsmanship*. Prentice Hall.

7. Nielsen, J. (1994). *Usability engineering*. Morgan Kaufmann.

8. Otwell, T. (2023). Laravel documentation. Retrieved from https://laravel.com/docs

9. Pressman, R. S., & Maxim, B. R. (2020). *Software engineering: A practitioner's approach* (9th ed.). McGraw-Hill.

10. Romero, C., & Ventura, S. (2010). Educational data mining: A review of the state of the art. *IEEE Transactions on Systems, Man, and Cybernetics, Part C (Applications and Reviews)*, 40(6), 601-618.

11. Russell, S., & Norvig, P. (2020). *Artificial intelligence: A modern approach* (4th ed.). Pearson.

12. Sommerville, I. (2015). *Software engineering* (10th ed.). Pearson.