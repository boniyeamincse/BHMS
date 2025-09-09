📋 BHMS Features — 79+ Modules 🌐 Super Admin (SaaS Level)

Dashboard (KPIs: hospitals, plans, revenue, enquiries)

Registered Hospitals (list, status, impersonation)

Hospital Types (categories)

Subscription Plans (trial/free/paid, monthly/yearly)

Plan Proration (adjust on switch)

Transactions (Stripe/PayPal logs)

Subscribers (newsletter/landing)

Enquiries (from landing page)

Front CMS (About, Services, Pricing, FAQ, landing text)

Settings (branding, SMTP, SMS, payment, favicon, locales)

🏥 Hospital Admin + Staff Core & Users

Dashboard (invoices, bills, payments, staff, beds)

Users & Roles (Doctor, Nurse, Receptionist, etc.)

Appointments & Scheduling

Doctor Departments

Doctors (profile, cases, payrolls)

Doctor Schedules (slots/availability)

Appointments (calendar/list)

Patient Care – OPD & IPD

OPD Module (Outpatient)

OPD Visits (history, revisits)

OPD Timelines (advice, docs)

OPD Diagnosis (reports, attachments)

Consultant Register

IPD Module (Inpatient)

Admissions (beds, insurance, dates)

Charges (per category)

Prescriptions (IPD)

IPD Payments

Advance Payments (deposits)

IPD Bill Generation (PDF export)

Clinical Records & Reports

Prescriptions (general)

Birth Reports

Death Reports

Operation Reports

Investigation Reports

Beds & Wards

Bed Types (VIP, AC, Non-AC, etc.)

Beds (create/manage)

Bed Assignments (auto availability)

Bed Graphic View (ward map, hover details)

Blood Bank

Blood Bags (stock per group)

Blood Donors (registry, dates)

Blood Donations (quantities)

Blood Issues (issue to patient)

Billing & Finance

Account Types (Credit/Debit)

Invoices (patients, discount, PDF)

Bills (auto patient/admission, PDF)

Payments (cash/cheque, due vs received)

Advance Payments (pre-admission)

Employee Payrolls (salaries per staff role)

Income Records

Expense Records

Charge Categories

Charges (per category)

OPD Charges (per doctor)

Diagnostics (Labs)

Radiology Categories

Radiology Tests

Pathology Categories

Pathology Tests

Diagnosis Categories

Patient Diagnosis Tests (PDF export)

Pharmacy & Inventory

Medicine Categories

Medicine Brands

Medicines (stock, details, side effects)

Pharmacy Bills (optional)

Inventory Categories (items)

Inventory Items

Item Stock (purchase)

Item Issue (deduct from stock)

Communication & Front Office

Mail Service (internal email)

SMS (Twilio bulk SMS by dept.)

Notice Boards

Testimonials (with images)

Call Logs (inbound calls)

Visitors Log

Postal Receive

Postal Dispatch

Telehealth & Meetings

Live Consultations (Zoom, patient joins)

Live Meetings (Zoom, staff only)

Content & Settings

Front Settings (hospital landing page)

Module On/Off (toggle availability)

Multi-Language (9+)

Multi-Currency (7+)

General Settings (branding, hospital info, hours)

Data Utilities

Export to Excel (lists)

Export to PDF (invoices, bills, diagnosis)

Types of Dashboards in BHMS 🌐 SaaS Level

Super Admin Dashboard

Number of registered hospitals

Active vs inactive hospitals

Subscription plan breakdown (trial/free/paid)

Revenue by month (Stripe/PayPal)

New enquiries & subscribers

System usage metrics (logins, API calls, uptime)

🏥 Hospital Level

Hospital Admin Dashboard

Finance overview: invoices, bills, payments, due vs received

Staff count (doctors, nurses, receptionists, pharmacists)

Patients admitted, discharged, OPD visits

Bed availability (occupied vs free)

Blood stock levels

Top income/expense categories (charts)

Notifications/announcements

Doctor Dashboard

Today’s appointments (calendar/list)

Patient cases assigned

Upcoming schedules/availability

Pending prescriptions

OPD/IPD patients under care

Tele-consultations scheduled

Receptionist Dashboard

Appointment requests (pending/approved)

Patient registrations (new vs returning)

Call log summary

Visitor log summary

Enquiries/tickets

Accountant Dashboard

Total revenue (month-to-date, year-to-date)

Pending bills & invoices

Payments received vs due

Payrolls processed/pending

Income vs expense charts

Case Handler Dashboard

Open patient cases

Admissions & discharges

Ambulance requests assigned

Case timelines pending updates

Nurse Dashboard

Bed occupancy (per ward)

Assigned patients & medication schedule

Pending vitals entry

Blood bank alerts (low stock)

Pharmacist Dashboard

Medicines stock (low, expired soon)

Prescriptions pending fulfilment

Sales/bills generated today

Supplier/restock alerts

Lab Technician Dashboard

Pending test requests (pathology, radiology, diagnosis)

Tests in progress vs completed

Reports pending review/sign-off

Equipment usage logs

Patient Dashboard (Portal)

Upcoming appointments

Bills/payments (due & history)

Prescriptions & reports available

IPD admission history

Notifications/messages from hospital

Tele-consultation links

---

## 🧪 Testing Scenarios

### Prerequisites
1. Ensure PostgreSQL is running and configured in `.env`
2. Run migrations and seeders: `php artisan migrate:fresh --seed`

### Test Users Created
- **Super Admin**: `superadmin@example.com` / `password`
- **Hospital Admin**: `admin@example.com` / `password`
- **Doctor**: `doctor@example.com` / `password`
- **Patient**: `patient@example.com` / `password`
- **Hospital**: `Test Hospital`

### Test Scenarios

#### 1. Super Admin Access
1. Login as Super Admin at `/login` with credentials above
2. Attempt to access hospital dashboard (should redirect or deny)
3. Verify Super Admin can see all hospitals/users (unscoped)

#### 2. Hospital Admin Dashboard
1. Login as Hospital Admin (`admin@example.com` / `password`)
2. Access `/hospital/dashboard`
3. Verify dashboard shows scoped metrics:
   - User count (3 for hospital: admin, doctor, patient)
   - Patient count (1)
4. Check multi-tenancy: Only hospital data visible

#### 3. Settings Module
1. Access `/hospital/settings` as logged-in hospital user
2. Verify module toggles are displayed
3. Check/uncheck boxes and submit
4. Refresh settings page to confirm updates saved

#### 4. Multi-Tenancy Verification
1. Login as different users and confirm scoped queries
2. Create a new user with hospital_id via Tinker: `php artisan tinker`
   ```php
   App\Models\User::create(['name'=>'Test User','email'=>'test2@example.com','password'=>Hash::make('password'),'hospital_id'=>1]);
   ```
3. Login as Hospital Admin and verify user count increased to 5

#### 5. Role-Based Access
1. Verify logged-in user's roles: `$user->roles` in Tinker
2. Test route protection: Attempt to access dashboard without auth (should redirect to login)

#### 6. Authentication & Session
1. Login and verify session persists
2. Logout and confirm session cleared
3. Test Sanctum API tokens if implementing frontend

### Running Tests
```bash
# Fresh installation
php artisan migrate:fresh --seed

# Run Laravel tests (if implemented)
php artisan test

# Tinker for manual testing
php artisan tinker
```

### Expected Results
- ✅ Dashboard displays correct scoped metrics
- ✅ Settings updates persist and toggle modules
- ✅ Multi-tenancy isolates data per hospital
- ✅ Authentication prevents unauthorized access
- ✅ Super Admin bypasses hospital scoping

### Troubleshooting
- If dashboard shows 0 counts, check user hospital_id assignments
- If login fails, verify hashed passwords in seeder
- Clear cache: `php artisan cache:clear` and restart PHP service
