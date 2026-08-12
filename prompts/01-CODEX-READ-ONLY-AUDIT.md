# EC-00 — Codex Read-only Environment Audit

## Context

อ่าน:
1. AGENTS.md
2. README.md
3. MANIFEST.json
4. DESIGN.md
5. docs/01 ถึง docs/11

Master Plan Final Approved  
งานนี้ Read-only เท่านั้น

## Scope

ตรวจ:
- Repository: `C:\xampp\htdocs\student-skill-exchange`
- `php -v`, `where php`, `php -m`
- PHP Extensions: pdo_mysql, mbstring, openssl, fileinfo, gd, tokenizer, xml, ctype, curl
- Composer, Node, npm, Git
- MySQL/MariaDB, InnoDB
- DB readiness:
  - student_skill_exchange
  - student_skill_exchange_test
- Git/Repository State
- Lock Files + SHA256
- Existing Laravel Project
- Writable storage/bootstrap cache ถ้ามี
- Queue/Mail Readiness

## Rules

- ห้ามสร้าง/แก้ไฟล์
- ห้ามติดตั้ง
- ห้ามสร้าง Laravel Project
- ห้ามสร้างฐานข้อมูล
- ห้าม Migration
- ห้ามแก้ PATH
- ห้าม Commit/Push
- ห้ามเริ่ม EC-1
- ห้ามเดา

Codex รันคำสั่ง Read-only เองเมื่อทำได้  
งาน Windows Admin/GUI ให้ระบุสำหรับผู้ใช้

## Output

1. Environment Summary
2. PHP and Extensions
3. Composer / Node / npm / Git
4. MySQL/MariaDB
5. Repository and Git State
6. Lock File Integrity
7. Existing Project State
8. Missing Requirements
9. Exact Repair Steps
10. Risks
11. Recommended Next Action

จบด้วย:
- READY TO START DAY 1
- BLOCKED — ENVIRONMENT NOT READY
- BLOCKED — DOCUMENT CONFLICT FOUND

หยุด ห้าม Implement
