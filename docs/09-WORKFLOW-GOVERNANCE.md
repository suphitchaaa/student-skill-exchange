# 09 — Workflow Governance

## Claude

- Planning only
- ห้ามเขียนโค้ด
- ห้ามแก้ไฟล์
- ห้ามสร้าง Implementation Prompt
- ห้ามเปลี่ยน Scope/DB Contract

## Codex

- Implement เฉพาะ Execution Checkpoint
- รัน Test
- Review Diff
- Report
- ห้าม Commit/Push เอง
- ห้ามทำงานถัดไป

## ChatGPT

- ตรวจ Claude Plan
- เขียน Prompt Codex
- ตรวจผล Codex
- คุม Scope/DB/UX/Code Rules
- ทำ Handoff เมื่อย้ายแชท

## User

- Manual Browser QA
- อนุมัติ Checkpoint
- รันงานที่ต้องใช้ Windows Admin/GUI/Password
- อนุมัติ Commit/Push

## Prompt Format

Header นอก Prompt:
- Model
- Task
- Effort
- Session
- Permission
- EC / Day
- Progress

Body:
- Context
- Scope
- Rules
- Validation
- Output / Stop

## Commit

- 1 Commit ต่อ Execution Checkpoint
- หลัง Automated Test + Manual QA ผ่าน
- ห้าม Push จนผู้ใช้สั่ง
- Lock File Change แยกจาก Feature Change

## Chat Handoff

เมื่อแชทหน่วง ช้า ยาว สับสน หรือเปลี่ยน Module:
- เตือนย้ายแชททันที
- สรุป Scope, State, Test, Git, Next EC, Progress
