# 02 — User Flow

## Guest

`Landing → Register/Login → Authentication → Role Check → Account Status Check → Correct Dashboard`

## First-time Student

`Login → Dashboard → Profile Incomplete Notice → Profile Form → Upload Image → Add Offered Skill → Add Wanted Skill → Ready to Search`

## Profile Image

Upload:
`Validate → Store New → Update DB`
- DB fail → Delete New File

Replace:
`Validate → Store New → Update DB + Commit → Delete Old File`
- DB fail → Delete New File
- Delete old fail → Log Defect

Delete:
`Read Old Path → Clear DB + Commit → Delete Old File`
- DB fail → Keep File
- Delete fail → Log Defect

DB และ Filesystem ไม่ใช่ Atomic Transaction เดียวกัน

## Skills

`My Skills → Offered/Wanted Tab → ค้นหาทักษะที่ใช้งานได้ → เลือกทักษะเดิมหรือเพิ่มชื่อใหม่ → Validate + Ownership → Save`

- ชื่อใหม่สร้างแถวใน `skills` หมวดหมู่ `ทั่วไป` และ `is_active = true` แล้วอ้างอิงจาก `user_skills.skill_id` ได้ทันที ไม่รอ Admin อนุมัติ
- ชื่อที่ Normalize แล้วตรงกับทักษะ inactive หรือ soft-deleted ห้ามนักศึกษาสร้างซ้ำหรือเปิดใช้งานเอง
- ทักษะใหม่ที่เพิ่มสำเร็จใช้กับ offered/wanted, การค้นหา, โปรไฟล์สาธารณะ และคำขอแลกเปลี่ยนได้ทันทีตามประเภททักษะ

## Search

`Search → Name/Skill/Faculty/Year Filter → Active Students Only → Exclude Current User → Results`

Empty State ต้องมีปุ่มล้างตัวกรอง

## Public Profile

`Search Result → Public Profile → View Offered/Wanted Skills → Start Exchange Request`

## Create Exchange Request

`Choose Sender Offered Skill → Choose Receiver Offered Skill → Format → Schedule → Message → Validate → Transaction → Duplicate Pending Guard → Create Pending → Observer → Queue`

## State Flow

- pending → accepted
- pending → rejected
- pending → cancelled
- accepted → completed

## Authorization

- Accept/Reject: Receiver
- Cancel: Sender
- Complete: Sender หรือ Receiver
- Admin: Read-only ต่อ Request State

## Suspended

`Protected Request → account.active → Suspended Page → Logout POST`

ห้าม Redirect Loop และห้าม Logout อัตโนมัติ
