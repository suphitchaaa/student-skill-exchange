# 08 — Code Structure Rules

## Routes

- Route Group ตาม Prefix/Name/Middleware
- ไม่มี Business Logic
- ห้าม Closure Route สำหรับ Domain Workflow

## Controllers

- รับ Request
- Validation
- Authorization
- เรียก Service/Model
- Response/View/Redirect

ห้าม Transaction Workflow ขนาดใหญ่

## Form Requests

ใช้กับ:
- Profile
- Profile Image
- User Skill
- Exchange Request
- Admin Skill

## Policies

ใช้กับ:
- Profile Ownership
- UserSkill Ownership
- ExchangeRequest View/Transition
- Admin Actions ตาม Scope

Role Middleware ไม่แทน Ownership Policy

## Services

สร้างเมื่อมี Transaction / Storage / Multi-step Rule:
- `ExchangeRequestService`
- `ProfileImageService`

ห้ามสร้าง Service สำหรับ CRUD เล็กที่ไม่มี Business Logic

## Observer

`ExchangeRequestObserver`:
- created / updated
- `wasChanged('status')`
- `getOriginal('status')`
- Dispatch หลัง Commit

ห้าม Authorization, State Transition หรือ Transaction หลัก

## Views

- Blade Layout/Partial/Component
- ห้าม Query และ Business Logic ใน View
- Action แสดงตาม Permission/State
- ไม่มี Dead UI

## JS / CSS

- Vanilla JS
- แยกไฟล์เมื่อ Logic มากพอ
- ห้าม Inline Script ใหญ่
- Bootstrap 5 เป็นฐาน
- Custom CSS แยก Tokens/Layout/Components/Page

## Thai Comment Lock

คอมเมนไทยแบบกระชับใน:
- Register Role/Status Rule
- Account Active Guard
- Image Compensation
- Safe Filename
- UserSkill Unique
- Search Active/Exclude Self
- Duplicate Pending Transaction
- State Transition
- Event Mapping
- Historical Integrity

ห้ามคอมเมนทุกบรรทัดหรือแปล Syntax

## Naming / Quality

- Class PascalCase
- Method/Variable camelCase
- DB snake_case
- Route Name dot notation
- ไม่มี Dead Code / Debug Dump / Secret
- Error Message ผู้ใช้เป็นภาษาไทย
- ตรวจ `git diff` ก่อนรายงาน
