# 11 — Approved Scope Decisions

## SD-1 Document Version

ใช้ชื่อชุดเอกสาร `V2.0` ตาม Manifest

## SD-2 Skill Restore

ไม่มี Restore Route, Button หรือ Menu  
Admin Skill ใช้ Soft Delete เท่านั้น

## SD-3 Notification

ใช้:
- Database Notification
- Queued Email

ไม่มี:
- Notification List Page
- Notification Route
- Notification Menu
- Badge ที่ลิงก์ไป Dead Page

## SD-4 Email Verification

คง `email_verified_at` แบบ nullable  
ไม่ทำ Verification Flow  
ไม่ใช้ MustVerifyEmail / verified middleware

## SD-5 Suspended User

- แสดงหน้าสถานะระงับภาษาไทย
- Session ยังอยู่
- มีปุ่ม Logout แบบ POST + CSRF
- ห้าม Redirect Loop
- ห้าม Logout อัตโนมัติ

## SD-6 Duplicate Pending

ซ้ำเมื่อ Pending และ 4 ฟิลด์ตรงกัน:
- sender_id
- receiver_id
- sender_user_skill_id
- receiver_user_skill_id

ไม่ซ้ำเมื่อ:
- คู่ทักษะต่างกัน
- ทิศทางกลับกัน
- รายการเดิมไม่ใช่ Pending

ตรวจภายใน Transaction + lockForUpdate  
ห้ามเพิ่ม Partial Unique Index

## Approved Clarifications

- `student_profiles`: ทุกฟิลด์นอกจาก user_id nullable
- `user_skills.description`: nullable
- `preferred_schedule`, `message`: required
- `responded_at`, `completed_at`, `profile_image`, `email_verified_at`: nullable
- Profile Incomplete ใช้ Notice ไม่เพิ่ม Middleware บังคับ
- Concurrent Race ห้ามรายงานว่าป้องกันได้ 100% เพราะไม่มี DB Unique Constraint สำหรับ Pending
