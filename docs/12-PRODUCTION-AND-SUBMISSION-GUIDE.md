# EC-12 — Production and Submission Guide

เอกสารนี้ใช้สำหรับ Production Readiness, การติดตั้งระบบ, การดูแล Queue, การสำรองฐานข้อมูล, Final Demo และ Submission Package ของ Student Skill Exchange

> เอกสาร `docs/01` ถึง `docs/11` ยังคงเป็น Approved Baseline และเอกสารฉบับนี้ไม่เปลี่ยน Scope Lock หรือ Database Contract

## 1. Environment Requirements

ระบบใช้:

- PHP 8.2 ขึ้นไป
- Laravel 12
- MySQL หรือ MariaDB
- Composer
- Node.js / npm

ก่อนติดตั้ง Production ให้สร้าง `.env` จาก `.env.example` และกำหนดค่าของเครื่องหรือ Hosting จริง

Production ต้องกำหนดอย่างน้อย:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_STORE=file
```

ห้าม commit `.env`, APP_KEY, Database Password หรือ credential จริงเข้าสู่ Git

## 2. Production Installation

ติดตั้ง PHP dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

ติดตั้ง frontend dependencies:

```bash
npm install
```

สร้าง `.env` จาก `.env.example` และกำหนดค่าตาม Production Environment

สร้าง Application Key:

```bash
php artisan key:generate
```

รัน Migration:

```bash
php artisan migrate --force
```

สร้าง Storage Link:

```bash
php artisan storage:link
```

Build frontend assets:

```bash
npm run build
```

## 3. Production Cache and Optimization

หลังตั้งค่า `.env` ถูกต้องแล้ว ให้รัน:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

หลัง Deployment หรือแก้ config สามารถล้าง cache ก่อนสร้างใหม่ด้วย:

```bash
php artisan optimize:clear
```

จากนั้นสร้าง cache ใหม่ตามขั้นตอน Production อีกครั้ง

## 4. Queue Worker

ระบบใช้ Database Queue สำหรับงาน Notification/Email แบบ asynchronous

เริ่ม Queue Worker:

```bash
php artisan queue:work
```

หลัง Deployment หรือมีการเปลี่ยน application code ให้สั่ง:

```bash
php artisan queue:restart
```

Production Server ควรใช้ Process Manager หรือ Service Manager เพื่อให้ Queue Worker ทำงานต่อเนื่องและเริ่มใหม่ได้เมื่อ process หยุด

ตรวจสอบ failed jobs:

```bash
php artisan queue:failed
```

หากมี failed job ต้องตรวจสาเหตุก่อนนำระบบส่งหรือสาธิต

## 5. Storage

ระบบมีการจัดการ Profile Image

Production ต้องรัน:

```bash
php artisan storage:link
```

และต้องตรวจว่า Web Server สามารถอ่าน/เขียน directory ที่ Laravel ใช้งานได้ตามสิทธิ์ที่เหมาะสม

## 6. Database Backup

ก่อน Deployment หรือการเปลี่ยนแปลง Production Database ควรสำรองฐานข้อมูลก่อนทุกครั้ง

ตัวอย่าง MySQL/MariaDB backup:

```bash
mysqldump -u your_database_user -p your_database_name > student_skill_exchange_backup.sql
```

ห้าม commit backup ที่มีข้อมูลจริงหรือข้อมูลส่วนบุคคลเข้าสู่ Git

## 7. Database Restore

ตัวอย่าง restore:

```bash
mysql -u your_database_user -p your_database_name < student_skill_exchange_backup.sql
```

ต้องตรวจชื่อฐานข้อมูลก่อน restore ทุกครั้ง เพื่อป้องกันการ restore ผิดฐาน

## 8. Database Separation

Development Database:

```text
student_skill_exchange
```

Testing Database:

```text
student_skill_exchange_test
```

ห้ามใช้ฐานข้อมูลเดียวกันระหว่าง Development และ Automated Tests

ก่อนรัน Full Test หลัง Cache/Optimize ต้องยืนยันว่า Test Process ชี้ไปที่:

```text
student_skill_exchange_test
```

เท่านั้น

หากพบว่า Testing Process ชี้ไป Development Database ให้หยุดทันทีและแก้ configuration ก่อนรัน test suite

## 9. Demo Data

`DatabaseSeeder` เรียก `DemoSeeder` สำหรับสร้างข้อมูลสาธิต

Demo Data ประกอบด้วย:

- Demo Admin 1 บัญชี
- Demo Students 12 บัญชี
- Skills ครบ 6 หมวด
- Offered/Wanted Skills
- Exchange Requests ตัวอย่าง 5 สถานะ:
  - pending
  - accepted
  - rejected
  - cancelled
  - completed

Demo Admin:

```text
Email: demo.admin@example.com
Password: password
```

Demo Student ตัวอย่าง:

```text
Email: demo.student1@example.com
Password: password
```

นักศึกษาตัวอย่างอื่นใช้รูปแบบ:

```text
demo.student2@example.com
...
demo.student12@example.com
```

และใช้รหัสผ่าน:

```text
password
```

### Important

DemoSeeder ใช้สำหรับ Demo/Testing Environment เท่านั้น

ห้ามรัน DemoSeeder บน Production Database ที่มีข้อมูลจริง

## 10. Demo Database Preparation

สำหรับฐานข้อมูลสาธิตที่แยกออกจาก Development/Production และได้รับอนุญาตให้สร้างใหม่เท่านั้น สามารถใช้ Seeder ตามขั้นตอนที่กำหนดสำหรับ Demo Environment

ห้ามใช้คำสั่ง destructive เช่น:

```text
migrate:fresh
db:wipe
migrate:reset
migrate:rollback
```

กับ Development หรือ Production Database ที่มีข้อมูลที่ต้องรักษา

## 11. Instructor Requirement Mapping

### 1. ผู้ใช้อย่างน้อย 2 กลุ่ม

ระบบรองรับ:

- Student
- Admin

แยก role ใน `users.role`

### 2. Middleware

ระบบใช้ Middleware และ Route Access Control เพื่อแยกสิทธิ์ Student/Admin และป้องกัน Suspended Account

### 3. Relational Database

ระบบใช้ MySQL/MariaDB และมี relational tables ตาม `docs/03-DATABASE-CONTRACT.md`

### 4. CRUD

ระบบมี CRUD สำหรับข้อมูลที่อยู่ใน Scope เช่น:

- Student Profile
- User Skills
- Admin Skill Management

### 5. Upload Image

Student สามารถ Upload Profile Image ได้

### 6. Replace/Delete Image และลบไฟล์เก่าจริง

Profile Image Flow รองรับ Upload, Replace และ Delete พร้อมจัดการไฟล์เก่าและ compensation เมื่อเกิด DB failure

### 7. Asynchronous Email ผ่าน Queue

Notification/Email ถูก dispatch ผ่าน Queue และไม่ทำ Blocking Mail ใน Controller

### 8. Observer

Exchange Request ใช้ Observer สำหรับ dispatch notification ตามการเปลี่ยนสถานะ

### 9. Feature Tests

ระบบมี Automated Feature Tests ครอบคลุม Auth, Profile, Skills, Search, Exchange Requests, Queue/Observer และ Admin

Baseline EC-11:

```text
100 tests / 412 assertions PASS
```

### 10. Production Configuration

EC-12 ครอบคลุม:

- `.env.example`
- APP_DEBUG=false
- Production Installation
- Queue Worker
- storage:link
- Cache/Optimize
- Backup/Restore
- Database Separation
- Final Validation

## 12. Final Automated Validation

ก่อนส่งงานต้องรัน:

```bash
php artisan test
```

```bash
vendor/bin/pint --test
```

```bash
npm run build
```

และ:

```bash
git diff --check
```

Full Test ต้องรันหลัง Production Cache/Optimization Validation โดยต้องยืนยัน Test DB Isolation ก่อน

## 13. Final Database Check

ก่อน Commit EC-12 ต้องตรวจ:

- Development DB ยังเป็น `student_skill_exchange`
- Test DB ยังเป็น `student_skill_exchange_test`
- Migration status ถูกต้อง
- Dev DB historical/manual QA data ไม่ถูกลบ
- jobs ไม่มีงานค้างที่ไม่คาดหมาย
- failed_jobs ว่าง
- ไม่มี orphan relationships ที่เกิดจาก EC-12
- ไม่มี schema change นอก Database Contract

## 14. Final Demo Run

Final Manual Demo ต้องครอบคลุมอย่างน้อย:

1. Guest Access
2. Register/Login
3. Student Profile
4. Profile Image Upload/Replace/Remove
5. Offered/Wanted Skills
6. Student Search
7. Public Profile
8. Create Exchange Request
9. Accept Request
10. Complete Request
11. Queue/Notification
12. Admin Login
13. Admin Dashboard
14. Student Management
15. Skill Management
16. Request Monitoring
17. Logout/Session sanity

Final Demo Run เป็น Manual QA ของผู้ใช้ และต้องดำเนินการก่อน EC-12 Commit

## 15. Submission Checklist

ก่อน Commit/Submission ต้องยืนยัน:

- [ ] `.env` ไม่ถูก tracked
- [ ] ไม่มี secret หรือ credential จริงใน repository
- [ ] `.env.example` พร้อมสำหรับ Production
- [ ] Production Guide ครบ
- [ ] Queue Worker Guide ครบ
- [ ] Storage Link Guide ครบ
- [ ] Cache/Optimize Guide ครบ
- [ ] Backup/Restore Guide ครบ
- [ ] Instructor Requirements ครบ 10 ข้อ
- [ ] Demo Data พร้อม
- [ ] Full Test PASS
- [ ] Pint PASS
- [ ] Vite Build PASS
- [ ] DB Check PASS
- [ ] Final Demo Run PASS
- [ ] `git diff --check` PASS
- [ ] ไม่มีงานนอก Scope Lock
- [ ] ไม่มี EC-13 หรือ feature ใหม่
- [ ] Git state พร้อม Commit EC-12

## 16. EC-12 Boundary

EC-12 เป็น Execution Checkpoint สุดท้ายของ Current Approved Plan

หลัง EC-12 ผ่านครบและ Commit แล้ว Current Execution Plan ถือว่าเสร็จสมบูรณ์

งานเพิ่มเติมหลังจากนั้นต้องจัดการเป็นงานหรือแผนใหม่ และห้ามสร้าง EC-13 โดยพลการ