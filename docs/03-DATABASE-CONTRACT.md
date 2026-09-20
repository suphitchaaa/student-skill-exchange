# 03 — Database Contract

เอกสารนี้ล็อก Schema, Field, Relationship, FK, Index, Unique และ Delete Behavior

## Tables

1. users
2. student_profiles
3. skills
4. user_skills
5. exchange_requests
6. notifications
7. jobs
8. failed_jobs

## users

- id
- name
- student_code unique
- email unique
- password
- role: student|admin
- status: active|suspended
- email_verified_at nullable
- remember_token
- timestamps
- indexes: role, status

## student_profiles

- id
- user_id unique
- faculty nullable
- major nullable
- year_level nullable, 1–8 เมื่อมีค่า
- bio nullable
- phone nullable
- contact_channel nullable
- profile_image nullable
- timestamps
- FK user_id → users cascade
- One-to-One

## skills

- id
- name
- normalized_name required, UNIQUE, `utf8mb4_bin`
- category
- is_active
- timestamps
- deleted_at
- Soft Deletes
- indexes: name, category, is_active; UNIQUE(normalized_name)
- `normalized_name`: ตัดช่องว่าง Unicode รอบชื่อ รวมช่องว่างภายในที่ติดกันเป็นหนึ่งช่อง แล้วทำ Unicode case folding; ไม่รวมคำแปลหรือคำพ้องความหมาย
- UNIQUE ครอบคลุมทุกแถว รวม inactive และ soft-deleted; นักศึกษาไม่สามารถสร้างชื่อซ้ำหรือเปิดใช้รายการเหล่านั้นเอง
- ทักษะที่นักศึกษาสร้างใช้ `category = ทั่วไป`, `is_active = true` และใช้งานได้ทันทีโดยไม่รอ Admin อนุมัติ
- ไม่มีฟิลด์ creator/source/moderation และไม่มี historical-name snapshot ในฟีเจอร์นี้

## user_skills

- id
- user_id
- skill_id
- skill_type: offered|wanted
- description nullable
- timestamps
- UNIQUE(user_id, skill_id, skill_type)
- user FK cascade
- skill FK restrict

## exchange_requests

- id
- sender_id
- receiver_id
- sender_user_skill_id
- receiver_user_skill_id
- learning_format: online|onsite|either
- preferred_schedule required
- message required
- status: pending|accepted|rejected|cancelled|completed
- responded_at nullable
- completed_at nullable
- timestamps

FK ทั้ง 4 ตัวใช้ restrict  
indexes:
- sender_id
- receiver_id
- status
- receiver_id + status
- sender_id + status

## Duplicate Pending

ซ้ำเมื่อ Pending และ 4 ฟิลด์ตรงกัน:
- sender_id
- receiver_id
- sender_user_skill_id
- receiver_user_skill_id

ตรวจใน Transaction + lockForUpdate  
ห้ามเพิ่ม Partial Unique Index เอง

## State Transition

- pending → accepted
- pending → rejected
- pending → cancelled
- accepted → completed

ห้ามย้อนสถานะ  
ห้าม Hard Delete Exchange Request

## Notifications / Queue

ใช้ Laravel Standard Schema  
Local: `QUEUE_CONNECTION=database`
