## Kiroku SLS (Student Logging System)

On-the-job training project for Mindanao Kokusai Daigaku (MKD) Learning Resource Center to streamline library entry/exit logging through barcode scanning. Built on the TALL stack for a responsive, real-time experience tailored to MKD librarians and students.

### Project Overview
- Replaces manual paper logs with barcode scans for students entering/leaving the MKD library.
- Centralizes attendance records for monitoring and quick lookups by staff.
- Designed during OJT to modernize the MKD library logging workflow.

### System Architecture
- **Stack:** Tailwind CSS (styling), Alpine.js (lightweight interactivity), Laravel (API, services, routing), Livewire (real-time UI bindings).
- **Layers:** Presentation (Livewire + Tailwind/Alpine UI), Application (Laravel services/jobs/policies), Domain (attendance sessions/records, students), Persistence (Eloquent models, MySQL SQLite).
- **Modules:** Student Management, Attendance Sessions, Attendance Records, Reports (per day/month/semester), Admin dashboard.

### Key Features
- **Barcode-Based Logging**
  - Scan student barcodes for time-in/time-out at the library entrance.
  - Validate and log entries against the student roster.
- **Student Management**
  - Manage student profiles (ID, name, course, year level).
  - Search and filter by name, course, year level.
- **Attendance Sessions & Records**
  - Track active sessions, capture hourly/daily/monthly/semestral summaries.
  - View and export attendance history for audits and monitoring.

### Project Status
- Actively developed and tested during OJT for MKD library operations.
- Focused on reliable scanning, accurate logs, and fast lookups for librarians.

### Technologies Used
- Tailwind CSS, Alpine.js, Laravel, Livewire
- MySQL, SQLite
- PHP, Composer, Node.js, npm
- Optional: queues/cache for future scaling; PHPUnit/Pest for automated testing

### License & Acknowledgements
- License: To be defined by project owners.
- Acknowledgements: Mindanao Kokusai Daigaku library staff, OJT mentors, and contributors to the Kiroku SLS initiative.