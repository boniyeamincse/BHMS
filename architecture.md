# BHMS Architecture Document

## Overview

The BHMS (Business Hospital Management System) is a multi-tenant SaaS platform built with Laravel 12, designed to streamline hospital operations. It supports multiple hospital tenants with isolated data, role-based access control, and comprehensive modules for patient care, billing, diagnostics, and administration. The system is built to scale horizontally and supports both web and API interfaces.

## Tech Stack

### Backend
- **Framework**: Laravel 12.0 (PHP ^8.2)
- **Authentication**: Laravel Sanctum (API tokens), Laravel UI (session-based)
- **Database**: MySQL/PostgreSQL/SQLite (configurable)
- **Caching**: Redis/Filesystem (Laravel default)
- **Queue**: Laravel Queue (database, Redis)
- **Mail**: SMTP via Laravel Mail

### Frontend
- **Build Tool**: Vite
- **Styling**: Tailwind CSS v4
- **JavaScript**: Axios (HTTP client)
- **Charts**: Chart.js
- **Icons/Modals**: Alpine.js (via Laravel UI)
- **Templates**: Blade templating engine

### Infrastructure
- **Web Server**: Apache/Nginx
- **Process Management**: Supervisor (for queues)
- **Version Control**: Git
- **Deployment**: Laravel Envoy, Docker optional

## System Architecture

### Components

#### 1. **Presentation Layer**
- **Web Frontend**: Blade templates with Tailwind CSS
- **Admin Dashboards**: Role-specific dashboards for Super Admin, Hospital Admin, Doctors, Nurses, etc.
- **API**: Sanctum-protected REST endpoints (optional)
- **Real-time**: WebSockets for live consultations (Zoom integration)

#### 2. **Application Layer**
- **Controllers**: MVC pattern with dedicated controllers per role/module
  - SaasController for super admin features
  - HospitalController for hospital settings
  - Role-specific dashboards (AccountantDashboard, DoctorDashboard, etc.)
- **Services**: Business logic encapsulated in controllers
- **Middleware**: Authentication, Tenant (Hospital) scoping, Role-based access
- **Traits**: HospitableScope for multi-tenancy

#### 3. **Data Layer**
- **Models**: Eloquent ORM with relationships
  - User, Hospital, Patient, Ward, Bed, etc.
  - Scopes for filtering (Active, Paid, Available)
  - Mutators for computed properties
- **Database Migrations**: Version-controlled schema
- **Seeders**: Test data and admin user creation
- **Factories**: Test data generation

### Architecture Patterns
- **MVC**: Model-View-Controller separation
- **Multi-tenancy**: Hospital-scoped data with foreign keys
- **Repository Pattern**: Implicit via Eloquent
- **Service Layer**: Business logic in controllers
- **Observer Pattern**: Event-driven updates (e.g., notifications)

### Multi-Tenant Design
- Hospitals are tenants with `hospital_id` on related tables
- Super Admin bypasses scoping
- Global Scope (`HospitalScope`) applied automatically
- Isolated data per hospital (users, patients, etc.)

## Database Schema

### Core Tables

#### Users & Authentication
- `users`: id, name, email, password, hospital_id, role, last_login_at
- `roles`: id, name (Super Admin, Hospital Admin, Doctor, Nurse, etc.)
- `user_roles`: user_id, role_id
- `personal_access_tokens`: Sanctum tokens

#### Multi-tenancy
- `hospitals`: id, name, settings_json, subscription_plan_id, status
- `hospital_types`: id, name (e.g., General, Specialized)
- `settings`: key-value pairs per hospital (SMTP, Branding, etc.)

#### Subscription & Billing
- `subscription_plans`: id, name, price, duration, features
- `transactions`: id, hospital_id, plan_id, amount, gateway (Stripe/PayPal)

#### Patient Care
- `patients`: id, hospital_id, ward_id, name, dob, admission_date
- `wards`: id, hospital_id, name
- `beds`: id, ward_id, status (available/occupied)
- `invoices`: id, patient_id, total, status
- `payments`: id, invoice_id, amount, method

#### Diagnostics & Services
- `blood_inventory`: id, hospital_id, blood_type, donor_info
- `announcements`: id, hospital_id, title, content, date

#### CMS & Communication
- `cms_content`: id, page, content, locale
- `enquiries`: id, name, email, message
- `subscribers`: id, email

### Relationships
- Hospital hasMany Users, Patients, Beds, etc.
- User belongsTo Hospital
- Patient belongsTo Hospital, Ward
- Ward hasMany Beds, belongsTo Hospital
- Invoice hasMany Payments, belongsTo Patient

### Indexing Strategy
- hospital_id on all tenant-scoped tables
- Composite indexes where necessary (hospital_id + status)

## Security Features

### Authentication & Authorization
- **Session Management**: Laravel sessions with secure cookies
- **Rate Limiting**: Throttled login attempts
- **Brute Force Protection**: Exponential backoff
- **Role-Based Access Control**: Middleware gates/policies
- **Tenant Isolation**: Automatic scoping prevents data leaks

### Data Protection
- **Password Hashing**: bcrypt with salts
- **CSRF Protection**: Token-based (middleware)
- **XSS Prevention**: Blade escaping, Input sanitization
- **SQL Injection**: Prepared statements (Eloquent)
- **Input Validation**: Form requests with Laravel validation

### Application Security
- **Dependency Scanning**: Composer audit
- **Session Fixation Prevention**: Regenerate session on login
- **Secure Headers**: Content Security Policy (CSP)
- **Log Monitoring**: Authentication events logged
- **API Keys**: Encrypted storage for third-party services (Stripe, Twilio, Zoom)

### Network Security
- **HTTPS**: Enforced with HSTS
- **IP Whitelisting**: Optional for admin areas
- **DDoS Protection**: CDN/waf integration
- **CORS**: Configured for API endpoints

## Deployment Considerations

### Environment Configuration
- **Environment Variables**: `.env` for secrets (keys, databases)
- **Image Storage**: Local/S3 for files
- **Cache**: Redis for performance
- **Queue**: Redis/database for async tasks

### Production Setup
1. **Server**: LAMP stack (Linux, Apache, MySQL, PHP)
2. **SSL Certificate**: Let's Encrypt or Commercial
3. **Backup**: Automated database/cron jobs
4. **Monitoring**: Logs, uptime checks
5. **CDN**: Static assets (CSS/JS/images)

### Scaling
- **Horizontal**: Stateless app servers with load balancer
- **Database**: Read replicas, sharding for high volume
- **Caching**: Redis cluster
- **File Storage**: S3/CDN for media

### Maintenance
- **Zero Downtime**: Blue-green deployments
- **Priming**: Cache warm-up after deployment
- **Rollback**: Previous version backup
- **Security Updates**: Regular composer/patch updates