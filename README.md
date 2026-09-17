# Student Skill Exchange

ระบบแลกเปลี่ยนทักษะระหว่างนักศึกษา พัฒนาด้วย Laravel สำหรับ Mini Project / Term Project

นักศึกษาสามารถระบุทักษะที่สอนได้และทักษะที่ต้องการเรียน ค้นหานักศึกษาคนอื่น ส่งคำขอแลกเปลี่ยนทักษะ และดำเนินกระบวนการแลกเปลี่ยนตั้งแต่ส่งคำขอจนเสร็จสมบูรณ์ โดยมีระบบ Student/Admin, Authorization, Image Management, Queue, Observer, Notifications และ Automated Tests

## Technology Stack

- Laravel / PHP
- MySQL หรือ MariaDB
- Blade Templates
- Bootstrap 5
- Vanilla JavaScript
- Bootstrap Icons
- Vite

## Roles

ระบบมี 2 บทบาทหลัก:

- Student
- Admin

สิทธิ์การใช้งานถูกแยกด้วย Middleware, Authorization และ Route Access Rules ตามเอกสารที่อนุมัติไว้ใน `docs/`

## Local Environment

Repository:

```text
C:\xampp\htdocs\student-skill-exchange
```

Development Database:

```text
student_skill_exchange
```

Testing Database:

```text
student_skill_exchange_test
```

ห้ามใช้ Testing Database และ Development Database ร่วมกัน

## Basic Installation

### 1. Install PHP dependencies

```bash
composer install
```

### 2. Install frontend dependencies

```bash
npm install
```

### 3. Create environment file

คัดลอก `.env.example` เป็น `.env` แล้วกำหนดค่าตาม environment ที่ใช้งานจริง

ห้าม commit `.env` หรือข้อมูล credential จริงเข้าสู่ Git

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Configure database

กำหนดค่าฐานข้อมูลใน `.env` ให้ถูกต้องก่อนรัน migration

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Create storage link

```bash
php artisan storage:link
```

### 8. Build frontend assets

```bash
npm run build
```

### 9. Run application

```bash
php artisan serve
```

## Automated Validation

Full test suite:

```bash
php artisan test
```

Code style validation:

```bash
vendor/bin/pint --test
```

Frontend production build:

```bash
npm run build
```

## Approved Documentation

ข้อกำหนด Scope, Database, UX/UI, Acceptance Tests, Route Access และ Workflow Governance อยู่ในโฟลเดอร์:

```text
docs/
```

ไฟล์ `docs/01` ถึง `docs/11` เป็นเอกสาร Approved Baseline และไม่ควรเปลี่ยนโดยไม่มีเหตุผลตาม Governance

## Current State

Current implementation status:

```text
EC-12 FINAL PASS
```
EC-12 started from baseline commit:
```text
b1e7376
```
Final EC-12 verification:

- Production Cache/Optimize PASS
- Test DB Isolation PASS
- Full Regression PASS — 100 tests / 412 assertions
- Pint PASS — 80 files
- Vite Production Build PASS
- Final Demo Data PASS
- Final Dev DB Check PASS
- Orphan Check PASS
- Manual Final Demo PASS
- Queue Check PASS — jobs 0 / failed_jobs 0
- Secret/.env Check PASS
- No Scope Lock violation found

EC-12 ผ่าน Final Validation ครบแล้ว และเป็น Execution Checkpoint สุดท้ายของ Current Approved Plan.

Current Execution Plan เสร็จสมบูรณ์แล้ว ไม่มี EC-13 ในแผนที่อนุมัติปัจจุบัน.

## Production and Submission

รายละเอียด Production deployment, Queue Worker, Cache/Optimization, Database Backup/Restore, Demo Data และ Submission Checklist จะอยู่ใน:

```text
docs/12-PRODUCTION-AND-SUBMISSION-GUIDE.md
```
