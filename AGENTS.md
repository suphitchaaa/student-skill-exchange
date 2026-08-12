# AGENTS.md — Student Skill Exchange

## 1. Authority

ลำดับอำนาจ:
1. คำสั่งล่าสุดที่ผู้ใช้อนุมัติอย่างชัดเจน
2. `AGENTS.md`
3. เอกสารใน `docs/`
4. Prompt ของ Execution Checkpoint ปัจจุบัน
5. โค้ดใน Repository

หากเอกสารขัดกัน ให้หยุดและรายงาน `BLOCKED — DOCUMENT CONFLICT FOUND`
ห้ามเดา ห้ามเปลี่ยน Contract เอง

## 2. Roles

- Claude: วางแผนและวิเคราะห์เท่านั้น ห้าม Implement
- Codex: Inspect, Implement, Test และ Review Diff ตาม Checkpoint ที่ได้รับอนุมัติ
- ChatGPT: ตรวจแผน คุม Scope และสร้าง Prompt ให้ Codex ทีละ Checkpoint
- ผู้ใช้: Manual Browser QA และอนุมัติก่อน Commit

## 3. Technology Lock

Backend:
- Laravel
- PHP
- MySQL หรือ MariaDB

Frontend:
- Blade Templates
- Bootstrap 5
- Vanilla JavaScript
- Bootstrap Icons เพียงชุดเดียว

Laravel:
- Authentication
- Middleware
- Authorization / Policy
- Form Request Validation
- Laravel Storage
- Queue
- Mail / Notification
- Observer
- Feature Tests

ห้ามเพิ่มโดยไม่ได้รับอนุญาต:
- React, Vue, Angular
- Livewire, Inertia
- Tailwind CSS
- SPA Framework
- WebSocket
- Package ที่ซ้ำกับ Laravel Standard Capability
- Package ที่เพิ่มความซับซ้อนโดยไม่จำเป็น

## 4. Required Reading Order

1. `docs/01-PROJECT-SCOPE-LOCK.md`
2. `docs/02-USER-FLOW.md`
3. `docs/03-DATABASE-CONTRACT.md`
4. `docs/04-UX-UI-RULES.md`
5. `docs/05-ACCEPTANCE-TESTS.md`
6. `docs/06-PHASE-PLAN.md`
7. `docs/07-ROUTE-ACCESS-MATRIX.md`
8. `docs/08-CODE-STRUCTURE-RULES.md`
9. `docs/09-WORKFLOW-GOVERNANCE.md`
10. `docs/10-APPROVED-MASTER-PLAN.md`
11. `docs/11-APPROVED-SCOPE-DECISIONS.md`

## 5. Development Method

ทุก Execution Checkpoint:

`Inspect → Plan → Implement → Automated Test → Review Diff → Report → Manual QA → Fix → Approval → Commit`

ห้าม:
- ทำ Checkpoint ถัดไปล่วงหน้า
- เพิ่ม Feature นอก Scope
- เปลี่ยน Database Contract
- Commit หรือ Push เอง
- รายงาน PASS หากไม่ได้ทดสอบจริง
- สร้าง Route, Button, Menu หรือ Page ที่ยังใช้งานไม่ได้

## 6. Code Quality Lock

- แยกไฟล์และความรับผิดชอบตาม Laravel Convention
- Controller ต้องบาง
- ใช้ Form Request สำหรับ Validation ที่เหมาะสม
- ใช้ Policy สำหรับ Permission และ Ownership
- ใช้ Service สำหรับ Transaction / State Transition / Storage Workflow
- Observer มีหน้าที่ Event Mapping และ Dispatch เท่านั้น
- ห้าม Query ใน Blade
- ห้าม Business Logic ใน View
- ตั้งชื่อไฟล์ คลาส เมธอด และตัวแปรให้สื่อความหมาย
- โค้ดสำคัญต้องมีคอมเมนภาษาไทยแบบกระชับ
- ห้ามคอมเมนทุกบรรทัดหรือคอมเมนซ้ำสิ่งที่โค้ดบอกอยู่แล้ว

## 7. UX/UI Lock

- Clean Campus Utility
- ห้ามดูเหมือน AI สร้างหรือ Generic AI SaaS
- ห้าม Emoji ทุกจุด
- ห้าม Gradient, Glow, Glassmorphism, Blob, Robot, Sparkle และภาพ 3D
- ใช้ Bootstrap Icons ชุดเดียว
- ภาษาไทย
- รองรับ 1366×768 ที่ Zoom 100%
- ไม่มี Horizontal Scroll ทั้งหน้า
- ไม่มี Dead Button/Menu/Route

## 8. Completion Report

1. สิ่งที่ทำ
2. ไฟล์ที่สร้าง
3. ไฟล์ที่แก้
4. Package พร้อมเหตุผล
5. คำสั่งที่รัน
6. ผล Automated Tests
7. Manual Browser QA ที่ต้องตรวจ
8. สิ่งที่ยังไม่ได้ทำ
9. ความเสี่ยง
10. Git Status
11. Boundary ไฟล์ที่พร้อมตรวจ

สถานะ:
- `READY FOR MANUAL QA`
- `PARTIAL — NOT READY TO COMMIT`
- `BLOCKED — NEEDS DECISION`
- `BLOCKED — DOCUMENT CONFLICT FOUND`
