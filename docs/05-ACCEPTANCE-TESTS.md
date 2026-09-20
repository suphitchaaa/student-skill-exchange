# 05 — Acceptance Tests

## Auth / Access

- Guest เข้า Student/Admin Dashboard ไม่ได้
- Student เข้า Admin ไม่ได้
- Admin เข้า Admin ได้
- Admin เข้า Student-only ไม่ได้
- Suspended ถูกบล็อก
- Logout POST + CSRF

## Profile / Image

- ดู/แก้โปรไฟล์ตนเองได้
- แก้ของผู้อื่นไม่ได้
- year_level 1–8
- Upload MIME/Extension/Size ถูกตรวจ
- Replace แล้ว DB ชี้ไฟล์ใหม่และไฟล์เก่าหาย
- DB fail หลัง Store → ไฟล์ใหม่ถูก Compensation
- Delete แล้ว DB Clear และไฟล์หาย
- Safe Filename
- Validation ภาษาไทย

## Skills

- Offered/Wanted CRUD
- Duplicate ถูกป้องกัน
- Ownership ถูกต้อง
- Inactive/Deleted Skill ใช้สร้างใหม่ไม่ได้
- นักศึกษาค้นหาและเลือกทักษะ active เดิม หรือเพิ่มชื่อใหม่ลง `skills` แล้วใช้ใน offered/wanted ได้ทันทีโดยไม่รอ Admin
- ทักษะใหม่มี `category = ทั่วไป`, `is_active = true` และค้นหา/แสดงในโปรไฟล์/ใช้ส่งคำขอได้ตามประเภททักษะ
- ชื่อที่ต่างเพียงช่องว่าง Unicode หรือรูปแบบตัวพิมพ์ชน `normalized_name` เดียวกัน รวมกรณี soft-deleted; ไม่รวมคำแปลหรือคำพ้องความหมายอัตโนมัติ
- ชื่อที่ชนกับ inactive/soft-deleted ต้องไม่ถูกสร้างใหม่หรือเปิดใช้งานโดยนักศึกษา
- Historical Integrity ไม่เสีย

## Search

- Exclude Current User
- Active Student Only
- ไม่แสดง Admin
- Filter Name/Skill/Faculty/Year
- Empty State มี Clear Filter

## Exchange Creation

- สร้าง Pending ได้
- ห้าม Self-request
- Skill Ownership/Type ถูกต้อง
- Receiver Active
- Duplicate Pending 4 Fields ถูกป้องกัน
- คู่ทักษะต่างกันส่งได้
- ทิศทางกลับกันส่งได้
- หลัง rejected/cancelled/completed ส่งใหม่ได้

## Transition

- Receiver Accept/Reject Pending
- Sender Cancel Pending
- Sender/Receiver Complete Accepted
- Third Party ทำไม่ได้
- Non-allowed State ทำไม่ได้
- responded_at / completed_at ถูกตั้ง
- ไม่มี Hard Delete

## Observer / Queue

- created → Receiver
- accepted → Sender
- rejected → Sender
- cancelled → Receiver
- completed → Both
- Non-status update ไม่แจ้ง
- Same status save ไม่แจ้ง
- Rollback ไม่มี Job
- Dispatch หลัง Commit
- Controller ไม่มี Blocking Mail

## Admin

- Dashboard
- Search Student
- Suspend/Activate
- Guard มีผลจริง
- Skill CRUD + Soft Delete
- Request Monitor Read-only
- Admin Accept/Reject ไม่ได้

## UX/UI

- ไม่มี Emoji
- ไม่มี AI-like Decoration
- Bootstrap Icons ชุดเดียว
- ไม่มี Dead UI
- Label/Validation/Empty State ครบ
- 1366×768 ไม่มี Horizontal Scroll ทั้งหน้า

## Production

- `.env` ไม่ถูก Commit
- `.env.example` พร้อม
- APP_DEBUG=false ใน Guide
- Queue Worker / Storage Link / Cache Guide
- Test DB แยกจาก Dev
- `php artisan test` ผ่าน
