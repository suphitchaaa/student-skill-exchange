# REVISED MASTER PLAN — STUDENT SKILL EXCHANGE V2.0
## ตอบตามข้อกำหนด PLAN APPROVED WITH REQUIRED REVISIONS

Read-only Planning เท่านั้น ไม่มีการเขียนโค้ด ไม่มีการแก้ไฟล์ ไม่มีการสร้าง Implementation Prompt

---

## 1. Revision Summary

| # | ประเด็นที่สั่งแก้ | สิ่งที่ผิดในแผนเดิม | สิ่งที่แก้แล้ว | หัวข้อที่ได้รับผลกระทบ |
|---|---|---|---|---|
| R-1 | จำนวน Route | แผนเดิม §12.2 และ §23 ระบุ "36 Route" และใช้จำนวนเป็น Completion Gate | ยืนยันใหม่ว่าเป็น **39 Route Entries** และ **ยกเลิกการใช้จำนวนเป็น Gate** เปลี่ยนเป็นตรวจ Method + URI + Middleware + Access ทีละแถว | §12.2, §23, §31 เดิม |
| R-2 | SD-6 | แผนเดิมไม่ได้นิยาม Duplicate Pending อย่างชัดเจน ปล่อยให้ตีความได้ | เพิ่ม SD-6 นิยาม 4 ฟิลด์ตรงกันทั้งหมด + ตรวจใน Transaction + ทิศทางกลับกันและคู่ทักษะต่างกันไม่ใช่รายการเดียวกัน | §6 R-2, §17 D3-C2, §21 |
| R-3 | Profile Image | แผนเดิมเขียนว่า "DB สำเร็จก่อน แล้วจึงแตะ Disk" ซึ่ง **ไม่ครอบคลุมกรณี Store สำเร็จแต่ DB ล้ม** และมีนัยว่า DB+Storage เป็น Transaction เดียว ซึ่งไม่จริง | เขียน Flow ใหม่ทั้ง 3 กรณีพร้อม **Compensation** และประกาศชัดว่า DB กับ Storage ไม่ใช่ Atomic Transaction เดียวกัน | §8.5, §16 D2-C2, §22 |
| R-4 | Observer | แผนเดิมใช้ `isDirty('status')` ซึ่งอ่านค่าก่อนบันทึกและไม่เหมาะกับ `updated` | เปลี่ยนเป็น `wasChanged('status')` ใน `updated` และใช้ `getOriginal('status')` เพื่ออ่านสถานะเดิม + บังคับ Dispatch หลัง Commit | §26, §17 D4-C2 |
| R-5 | จำนวน Checkpoint | แผนเดิมมี 20 Checkpoint ซึ่งทำให้ต้อง Manual QA และ Commit 20 รอบ ถี่เกิน Natural Boundary | ลดเหลือ **12 Execution Checkpoints + 1 Day 0 Audit Gate** โดยเก็บ Boundary ย่อยเดิมไว้เป็น **Internal Boundary** สำหรับลำดับงานเท่านั้น ไม่ผูกกับ QA/Commit | §10, §23 |
| R-6 | Environment Timing | แผนเดิมวาง Environment ไว้เป็น D1-C1 ทำให้เวลาซ่อมกิน Day 1 | ย้าย Environment Audit **และ Repair** ไปอยู่ใน Day 0 ทั้งหมด เริ่มนับ Day 1 หลังได้ `READY TO START DAY 1` และเพิ่มกฎรายงานเมื่อเวลาซ่อมล้นเข้าวันพัฒนา | §13, §14 |

**ส่วนที่ไม่เปลี่ยน** ให้อ้างอิงแผนเดิมตามเดิม ได้แก่:
- §1–§3 Documents Reviewed / SHA256 / Conflict Report
- §4 Feasibility Verdict (ยังคง FEASIBLE)
- §5 Assumptions A-1 ถึง A-12
- §7 Critical Path
- §8.1–§8.4 System Flow (เฉพาะ §8.5 ถูกแทนที่ด้วยข้อ 4 ของเอกสารนี้)
- §9 Development Flow for Codex
- §11 Dependency Map
- §12.1 ลำดับ Migration
- §21 Test Strategy (จำนวน Test คงเดิม เพียงย้ายไปผูกกับ Execution Checkpoint ใหม่)
- §22 Manual QA Standing Checklist
- §24 UX/UI Execution Plan
- §25 Code Structure & Thai Comment Enforcement
- §27 Security & Authorization Plan
- §28 Git Strategy
- §29 Risk & Recovery Plan

---

## 2. Updated Scope Decisions SD-1 ถึง SD-6

| # | ประเด็น | มติที่อนุมัติ | ผลต่อการ Implement |
|---|---|---|---|
| **SD-1** | ชื่อชุดเอกสาร | ใช้ **V2.0** ตาม `MANIFEST.json` | ทุกการอ้างอิงในรายงาน Handoff และ Commit ใช้ V2.0 · ห้ามแก้ Manifest |
| **SD-2** | Skill Restore | **ไม่มี Restore** | `Admin/SkillController` มีเฉพาะ index, create, store, edit, update, destroy(soft delete) · ไม่มี Route, ปุ่ม หรือเมนู Restore · ทักษะที่ Soft Delete แล้วจะไม่ปรากฏใน Dropdown ของนักศึกษา แต่ยังคงอ้างอิงในประวัติได้ |
| **SD-3** | Notification | ใช้ **Database Notification + Queued Email** แต่ **ไม่มีหน้า Notification แยก** | บันทึกลงตาราง `notifications` และส่งเมลผ่าน Queue · **ไม่สร้าง Route, หน้า หรือเมนูรายการแจ้งเตือน** · ไม่มี Badge นับที่ลิงก์ไปหน้าที่ไม่มีอยู่ · ผู้ใช้รับรู้ผลผ่านอีเมลและผ่านหน้า `/exchange-requests` ตามปกติ |
| **SD-4** | `email_verified_at` | **คงฟิลด์ไว้ ไม่ทำ Verification Flow** | Migration มีฟิลด์ตาม Contract · ไม่ใช้ `MustVerifyEmail` · ไม่มี Middleware `verified` · ค่าเป็น null ตลอด และต้องไม่มี Guard ใดพึ่งพาฟิลด์นี้ |
| **SD-5** | Suspended User | **เห็นหน้าแจ้งเหตุผลภาษาไทย + ปุ่ม Logout แบบ POST** | `EnsureAccountIsActive` ไม่ทำ Logout อัตโนมัติ แต่ Redirect ไปหน้าสถานะระงับ · หน้านั้นต้องเข้าถึงได้ขณะ Login อยู่ · มีเฉพาะปุ่มออกจากระบบ (POST + CSRF) ไม่มีเมนูอื่น · ต้องกัน Loop Redirect ระหว่างหน้าระงับกับ Middleware |
| **SD-6** | Duplicate Pending | **ซ้ำเมื่อ `sender_id` + `receiver_id` + `sender_user_skill_id` + `receiver_user_skill_id` ตรงกันทั้งหมด และสถานะเป็น `pending`** | รายละเอียดด้านล่าง |

### SD-6 — นิยามและกฎการบังคับใช้

**ถือว่าซ้ำ** เมื่อมีแถวใน `exchange_requests` ที่ `status = 'pending'` และค่าทั้ง 4 ฟิลด์ตรงกันครบ

**ไม่ถือว่าซ้ำ** ในกรณีต่อไปนี้ (ต้องสร้างได้):
| กรณี | เหตุผล |
|---|---|
| A→B ทักษะคู่หนึ่ง กับ A→B ทักษะอีกคู่หนึ่ง | คู่ทักษะต่างกัน คนละคำขอ |
| A→B กับ B→A แม้ใช้ทักษะชุดเดียวกัน | ทิศทางกลับกัน ผู้ส่งและผู้รับสลับกัน คนละรายการ |
| A→B คู่ทักษะเดิม เมื่อรายการเดิมเป็น `rejected` / `cancelled` / `completed` | ไม่มี pending ค้าง จึงส่งใหม่ได้ |
| A→B เปลี่ยนเฉพาะ `sender_user_skill_id` | ฟิลด์ไม่ครบ 4 ตัว |

**กลไกบังคับ:**
1. ตรวจภายใน `DB::transaction()` เท่านั้น ห้ามตรวจนอก Transaction แล้วค่อยเข้า
2. Query Guard ใช้เงื่อนไข 4 ฟิลด์ + `status = 'pending'` พร้อม `lockForUpdate()`
3. Composite Index ที่มีอยู่ (`sender_id`,`status`) ช่วยให้ Query นี้ใช้ Index ได้ ไม่ต้องเพิ่ม Index ใหม่
4. **ห้ามเพิ่ม Partial Unique Index** ตาม `03-DATABASE-CONTRACT §7`
5. **ความเสี่ยงคงเหลือที่ต้องยอมรับและรายงาน:** เมื่อไม่มี Unique Index ระดับ DB การกันซ้ำอาศัย Lock ของ InnoDB บน Index Range ซึ่งกันได้ในทางปฏิบัติแต่ไม่ใช่การรับประกันระดับ Schema — ต้องมี Test ยิงซ้ำและมี Log เมื่อพบการชนกัน
6. เมื่อพบซ้ำ ต้องคืน Validation Error ภาษาไทยที่ Field ไม่ใช่ Exception ดิบ

**คอมเมนไทยบังคับ ณ จุดนี้:** อธิบายว่าทำไมต้องตรวจซ้ำภายใน Transaction และทำไมนิยามซ้ำจึงต้องครบทั้ง 4 ฟิลด์

---

## 3. Corrected Route Validation Rule

### 3.1 จำนวนที่ถูกต้อง

นับจาก `docs/07-ROUTE-ACCESS-MATRIX.md` ทีละแถว:

| ส่วน | จำนวนแถว |
|---|---|
| §1 Public/Auth | 6 |
| §2 Student | 20 |
| §3 Admin | 13 |
| **รวม** | **39 Route Entries** |

ตัวเลข 36 ในแผนเดิม **ผิด** ยกเลิกและแทนที่ด้วย 39

### 3.2 เหตุผลที่ห้ามใช้จำนวนเป็น Completion Gate

| ปัญหา | ตัวอย่าง |
|---|---|
| แถวที่เขียน `PUT/PATCH` อาจถูก Register เป็น 1 Route ที่รับสองเมธอด หรือ 2 Route แยกกัน | `PUT/PATCH /profile` |
| Laravel เพิ่ม Route ระบบเองได้ | `storage/{path}`, `sanctum/*`, `up` |
| หน้าแจ้งสถานะระงับตาม SD-5 เป็น Route ที่จำเป็นแต่ไม่ปรากฏใน Matrix | ต้องรายงานเป็น Route ส่วนเกินที่ได้รับอนุมัติ |
| จำนวนตรงไม่ได้แปลว่า Middleware ถูก | Route ครบ 39 แต่ลืม `account.active` หนึ่งกลุ่มก็ยังนับได้ 39 |

**ดังนั้นจำนวน Route ใช้เป็นได้แค่สัญญาณเตือน ไม่ใช่เกณฑ์ผ่าน**

### 3.3 กฎการตรวจใหม่ (บังคับใช้แทน)

Codex ต้องตรวจ **ทีละแถวของ Matrix** ด้วย 4 มิติ:

| มิติ | วิธีตรวจ | เกณฑ์ผ่าน |
|---|---|---|
| **Method** | `php artisan route:list --json` | เมธอดที่ Register ครอบคลุมที่ Matrix ระบุ และ **ไม่มีเมธอดเกิน** โดยเฉพาะห้ามมี GET บน Route ที่เปลี่ยนสถานะ |
| **URI** | เทียบสตริง | ตรงตาม Matrix (ชื่อ Route ปรับตาม Convention ได้ แต่ URI ต้องตรง) |
| **Middleware** | อ่าน Middleware Stack ของแต่ละ Route | Student = `auth`, `role:student`, `account.active` ครบสามตัว · Admin = `auth`, `role:admin`, `account.active` ครบสามตัว · Guest Route = `guest` · Logout = `auth` |
| **Access** | Feature Test เชิงลบ | ยิงด้วย Role ที่ไม่ควรเข้าได้ → ต้อง 403 · ยิงโดยไม่ Login → Redirect ไป Login · ยิงด้วยบัญชี Suspended → เข้าหน้าแจ้งระงับ |

**Route ส่วนเกินที่อนุญาต** ต้องประกาศเป็นรายการชัดเจนในรายงาน:
1. Route ระบบของ Laravel
2. หน้าแจ้งสถานะระงับตาม SD-5
3. ไม่มีอย่างอื่น — ถ้าพบ Route อื่นที่ไม่อยู่ใน Matrix และไม่อยู่ในสองข้อนี้ ให้รายงาน `BLOCKED — NEEDS DECISION`

**Route ที่ต้องยืนยันว่า "ไม่มี":**
- ไม่มี Route ใดที่ Admin ใช้เปลี่ยนสถานะ Exchange Request
- ไม่มี Route Restore Skill (SD-2)
- ไม่มี Route หน้ารายการแจ้งเตือน (SD-3)
- ไม่มี Route Email Verification (SD-4)

**ตรวจเมื่อไร:** ทุกสิ้น Execution Checkpoint ที่แตะ `routes/web.php` และตรวจเต็มทั้ง 39 แถวอีกครั้งที่ EC-10 และ EC-11

---

## 4. Corrected Profile Image Flow

### 4.1 ข้อเท็จจริงที่ต้องประกาศในโค้ดและในรายงาน

> **DB Transaction ไม่ครอบคลุม Filesystem** การ Rollback ฐานข้อมูลไม่ย้อนไฟล์ที่เขียนลงดิสก์แล้ว และการลบไฟล์สำเร็จไม่ได้แปลว่า DB สำเร็จ ทุกกรณีที่สองระบบไม่สอดคล้องกันต้องมี **Compensation** ที่เขียนไว้อย่างตั้งใจ ห้ามพึ่ง Transaction เพียงอย่างเดียว

ข้อความนี้ต้องปรากฏเป็นคอมเมนไทยแบบกระชับที่หัว `ProfileImageService`

### 4.2 Upload

```
Validate (MIME จริง + Extension + Size)
  → Generate Safe Filename (ห้ามใช้ชื่อจาก Client)
  → Store New File
  → Update DB
```
| ผลลัพธ์ | การกระทำ |
|---|---|
| DB สำเร็จ | Commit → Success |
| **DB ล้มเหลว** | **Delete New File** → โยนต่อ → แสดงข้อความไทย |
| Store ล้มเหลว | ไม่แตะ DB → แสดงข้อความไทย |

### 4.3 Replace

```
Validate
  → Store New File
  → Update DB + Commit
  → Delete Old File
```
| ผลลัพธ์ | การกระทำ |
|---|---|
| DB สำเร็จ | Commit → จากนั้นจึง **Delete Old File** |
| **DB ล้มเหลว** | **Delete New File** → Old File ยังอยู่ → DB ยังชี้ไฟล์เดิมที่มีอยู่จริง |
| Delete Old File ล้มเหลว | **ไม่ Rollback** เพราะ DB ถูกต้องแล้ว → Log + รายงานเป็น Defect (ไฟล์กำพร้า) |

หลักสำคัญ: **ห้ามลบไฟล์เก่าก่อน Commit เด็ดขาด** และ **ห้ามลบไฟล์เก่าภายใน Transaction**

### 4.4 Delete

```
Read Old Path (เก็บไว้ก่อน)
  → Clear DB Path + Commit
  → Delete Old File
```
| ผลลัพธ์ | การกระทำ |
|---|---|
| DB สำเร็จ | Commit → Delete Old File |
| DB ล้มเหลว | ไม่แตะไฟล์ → แสดงข้อความไทย |
| **Delete File ล้มเหลว** | **Log + รายงาน Defect** ไม่ย้อน DB เพราะสถานะที่ผู้ใช้เห็น (ไม่มีรูป) ถูกต้องแล้ว |

### 4.5 หลักการที่สรุปได้

| หลักการ | เหตุผล |
|---|---|
| DB เป็นแหล่งความจริง | ผู้ใช้เห็นสิ่งที่ DB บอก ไฟล์กำพร้าไม่กระทบการใช้งาน แต่ DB ชี้ไฟล์ที่ไม่มีอยู่กระทบทันที |
| ยอมให้เกิดไฟล์กำพร้า ไม่ยอมให้เกิด Broken Reference | ไฟล์กำพร้าเป็น Defect ระดับ Low · Broken Reference เป็น Defect ระดับ High |
| Compensation ทุกเส้นทางที่ Store สำเร็จแต่ DB ล้ม | ป้องกันขยะสะสมจากการล้มเหลวปกติ |
| ความล้มเหลวของการลบไฟล์ต้อง Log เสมอ | ต้องตรวจสอบย้อนหลังได้ ห้าม Suppress เงียบ |

### 4.6 Test ที่ต้องเพิ่มจากแผนเดิม

| # | เคส | คาดหวัง |
|---|---|---|
| IM-1 | Upload แล้วบังคับให้ DB ล้ม | ไฟล์ใหม่ต้องไม่เหลือบน Disk |
| IM-2 | Replace แล้วบังคับให้ DB ล้ม | ไฟล์ใหม่ถูกลบ · ไฟล์เก่ายังอยู่ · DB ยังชี้ไฟล์เก่า |
| IM-3 | Replace สำเร็จ | DB ชี้ไฟล์ใหม่ · ไฟล์เก่าหายจาก Disk |
| IM-4 | Delete สำเร็จ | DB Clear · ไฟล์หายจาก Disk |
| IM-5 | Delete แล้วไฟล์หายไปก่อนแล้ว | ไม่โยน Exception ให้ผู้ใช้ · DB Clear สำเร็จ · มี Log |

รวมกับเคสเดิม (MIME/Extension/Size/Ownership/Safe Filename) เป็น 10 เคสในหมวดรูปโปรไฟล์

---

## 5. Corrected Observer / After-commit Flow

### 5.1 โครงสร้างที่ถูกต้อง

```
Controller
  → ExchangeRequestService (เปิด Transaction)
      → ตรวจสถานะปัจจุบันซ้ำ
      → Model Save
      → Commit  ─────────────────┐
                                 │
  ExchangeRequestObserver        │  (ยิงตอน save แต่ Dispatch ถูกหน่วง)
      created / updated          │
      → กำหนดผู้รับแจ้งเตือน      │
      → Dispatch ──────────────→ ┘ ทำงานจริงหลัง Commit เท่านั้น
                                 ↓
                         jobs table → Worker → database + mail
```

### 5.2 กฎการตรวจ Event

| จุด | ใช้ | ห้ามใช้ | เหตุผล |
|---|---|---|---|
| `updated()` ตรวจว่าสถานะเปลี่ยนจริง | **`wasChanged('status')`** | `isDirty('status')` | ใน `updated` การบันทึกเกิดขึ้นแล้ว · `isDirty` สะท้อนสถานะก่อนบันทึกและอาจให้ผลไม่ตรงหลัง Save |
| อ่านสถานะเดิมเพื่อดู Transition | **`getOriginal('status')`** | ค่าที่ส่งมาจาก Request | ต้องอ่านจาก Model ไม่ใช่จาก Input ที่ผู้ใช้ควบคุมได้ |
| ตรวจ Transition | เทียบ `getOriginal('status')` → `status` | เดาจากสถานะปลายทางอย่างเดียว | `pending→accepted` และ `accepted→completed` ต้องแยกกันชัด |

### 5.3 กฎ After-commit

| ข้อ | กฎ |
|---|---|
| 1 | Notification ทุกคลาสประกาศ `ShouldQueue` และตั้งค่าให้ Dispatch หลัง Commit |
| 2 | Job ที่ Dispatch จาก Observer ต้องใช้กลไก after-commit เช่นเดียวกัน |
| 3 | ห้าม Dispatch แบบทันทีขณะ Transaction ยังเปิดอยู่ เพราะ Worker อาจหยิบงานไปอ่านข้อมูลที่ยัง Commit ไม่เสร็จ แล้วล้มลง `failed_jobs` |
| 4 | Payload ส่งเฉพาะ `exchange_request_id` และค่าที่จำเป็นต่อการแสดงผล ห้ามยัด Model เต็มก้อน ห้ามใส่ข้อมูลอ่อนไหว |
| 5 | ห้ามส่ง Mail แบบ Blocking ใน Controller, Service หรือ Observer |

### 5.4 ขอบเขต Observer (เข้มเท่าเดิม)

**ทำได้:** ตรวจ Event → ตรวจ `wasChanged('status')` → อ่าน `getOriginal('status')` → เลือกผู้รับ → Dispatch

**ห้าม:** Authorization · State Transition · เปิด Transaction หลัก · Query ซับซ้อน · สร้างข้อความแจ้งเตือนซ้ำหลายที่ · แก้ไข Model อื่น

### 5.5 Event Mapping (คงเดิม)

| Event | เงื่อนไข | ผู้รับ |
|---|---|---|
| `created` | — | Receiver |
| `updated` | `wasChanged('status')` และ original=`pending`, new=`accepted` | Sender |
| `updated` | original=`pending`, new=`rejected` | Sender |
| `updated` | original=`pending`, new=`cancelled` | Receiver |
| `updated` | original=`accepted`, new=`completed` | Sender + Receiver |
| `updated` | ไม่เข้าเงื่อนไขข้างต้น | ไม่ทำอะไร |

### 5.6 Test ที่ต้องเพิ่มจากแผนเดิม

| # | เคส | คาดหวัง |
|---|---|---|
| OB-1 | แก้ฟิลด์ที่ไม่ใช่ `status` (เช่น `preferred_schedule`) | ไม่มีการแจ้งเตือนใด ๆ |
| OB-2 | บันทึกซ้ำด้วยสถานะเดิม | `wasChanged` เป็นเท็จ ไม่แจ้งเตือน |
| OB-3 | Transaction ที่ Rollback | ไม่มี Job เข้าคิว |
| OB-4 | Transition สำเร็จ | มี Job เข้าคิวหลัง Commit จำนวนครั้งถูกต้อง (นับ ไม่ใช่แค่ยืนยันว่ามี) |
| OB-5 | `completed` | มีผู้รับ 2 ราย ไม่ใช่ 1 |

---

## 6. Revised Checkpoint Matrix — 12 Execution Checkpoints

**นิยามที่ต้องแยกให้ชัด:**
- **Internal Boundary** = ลำดับงานภายในของ Codex ใช้จัดคิวและตรวจ Diff ตัวเอง **ไม่มี** Manual QA และ **ไม่มี** Commit
- **Execution Checkpoint (EC)** = หน่วยที่มี Manual Browser QA + Approval + Commit **หนึ่งครั้ง**

รวม **12 Execution Checkpoints** + **1 Day 0 Audit Gate** (ไม่มี Commit โค้ด)

| EC | วัน | ชื่อ Execution Checkpoint | Internal Boundary ที่รวมอยู่ | Automated Test | Manual QA | Commit |
|---|---|---|---|---|---|---|
| **EC-0** | 0 | Environment Audit & Repair | Audit · Repair · Re-audit | — | ผู้ใช้ยืนยันผลคำสั่ง | ไม่มี |
| **EC-1** | 1 | Foundation & Database Contract | Laravel Install · `.env`/`.env.testing` · storage link · locale/timezone · Migration 8 ตาราง · Model · Relationship · Factory · Seeder | Model/Relationship · `migrate:fresh` ทั้ง 2 DB | เปิดหน้าแรกได้ · ตรวจโครงสร้างตารางใน DB | 1 |
| **EC-2** | 1 | Auth, Access Control & App Shell | Register/Login/Logout · `role` · `account.active` · หน้าสถานะระงับ (SD-5) · Route Group · Layout/Sidebar/Topbar · CSS Token · Dashboard 2 ฝั่ง | `tests/Feature/Auth/` (10 เคส) | Login 2 Role · Suspended เห็นหน้าแจ้งเหตุผล · 1366×768 | 1 |
| **EC-3** | 2 | Student Profile & Profile Image | Profile show/edit/update · Form Request · Policy · Profile Incomplete Notice · `ProfileImageService` Upload/Replace/Delete + Compensation | `tests/Feature/Profile/` (16 เคส รวม IM-1…IM-5) | แก้โปรไฟล์ · เปลี่ยนรูปแล้วตรวจไฟล์บน Disk จริง · ลบรูป | 1 |
| **EC-4** | 2 | User Skills CRUD | My Skills Tabs · Form Request · Policy · Unique Guard · ข้อความไทยเมื่อซ้ำ · Sidebar เพิ่มเมนู | `tests/Feature/Skills/` (7 เคส) | เพิ่ม/แก้/ลบทักษะ · เพิ่มซ้ำถูกปฏิเสธ | 1 |
| **EC-5** | 3 | Search & Public Profile | Search + Filter 4 แบบ · Query Rule · Student Card · Skill Tag Overflow · Empty State · Pagination · Public Profile | `tests/Feature/Search/` (6 เคส) | ค้นหา · Empty State + ปุ่มล้างตัวกรอง · ตัวเองไม่โผล่ | 1 |
| **EC-6** | 3 | Exchange Request Creation & Listing | Create Form · Form Request · `ExchangeRequestService::create` · Transaction · Duplicate Guard ตาม SD-6 · Tabs 3 แท็บ · Detail · View Policy | Create (11 เคส รวมเคส SD-6) · Ownership (2) · Historical (1) | ส่งคำขอ · ส่งซ้ำถูกกัน · ทิศทางกลับกันส่งได้ · เห็นเฉพาะของตัวเอง | 1 |
| **EC-7** | 4 | State Transition & Request UX States | Policy 4 เมธอด · Service 4 เมธอด · Transaction · `responded_at`/`completed_at` · 4 Route PATCH · Badge 5 สถานะ · Confirmation · Double-submit Protection · Empty State 3 แท็บ | Transition Matrix (16) · Field (3) | ตอบรับ/ปฏิเสธ/ยกเลิก/เสร็จสิ้น · ปุ่มแสดงตามสิทธิ์และสถานะ · กดรัวไม่ซ้ำ | 1 |
| **EC-8** | 4 | Observer, Queue & Notification | Observer (`wasChanged`/`getOriginal`) · Notification ตาม Mapping · After-commit Dispatch · Mail แบบ Queue | Observer/Queue (11 เคส รวม OB-1…OB-5) | เปิด `queue:work` · ตรวจ `notifications` · ตรวจ `failed_jobs` ว่าง | 1 |
| **EC-9** | 5 | Admin Dashboard & Student Management | Admin Dashboard (นับจริง ไม่มี Chart ปลอม) · Recent Requests · Student List/Search/Show · Suspend/Activate + Confirmation | `tests/Feature/Admin/` ส่วนนักศึกษา (6 เคส) | Suspend แล้วทดสอบข้าม Session ว่านักศึกษาถูกบล็อกจริง | 1 |
| **EC-10** | 5 | Admin Skill Management, Request Monitoring & Demo Data | Skill CRUD + Soft Delete (ไม่มี Restore) · ข้อความไทยเมื่อลบไม่ได้ · Monitor List/Filter/Detail Read-only · Truncate + Title · Demo Seeder · **ตรวจ Route Matrix เต็ม 39 แถว** | Admin ส่วนที่เหลือ (6 เคส) · Route/Middleware Assertion เต็ม | หน้า Monitor ไม่มีปุ่มเปลี่ยนสถานะ · ตารางที่ 1366×768 · Skill ที่ปิดใช้เลือกไม่ได้ | 1 |
| **EC-11** | 6 | Full Regression & Defect Repair | Suite เต็ม · Manual Regression 12 หมวด · Security/Storage/Queue/Responsive Sweep · Defect Repair · Cleanup · ตรวจคอมเมนไทย | `php artisan test` ทั้งชุด | Full User Flow ทั้งระบบ | 1 |
| **EC-12** | 7 | Production Readiness & Submission Package | `.env.example` · Production Guide · Queue Worker Guide · storage link · Cache/Optimize · Backup Instruction · Final Demo Data · Final Diff · Final Demo Run · เอกสารส่ง | Suite เต็มอีกครั้งหลัง Cache | Final Demo Run ตั้งแต่ Guest ถึง Admin | 1 |

**สรุปจำนวน Commit: 12 ครั้ง** (EC-1 ถึง EC-12) — ลดจากแผนเดิม 20 ครั้ง

**กฎประกอบ:**
- ห้าม Manual QA หรือ Commit ระดับไฟล์เดียวหรืองานย่อยเดียว
- ถ้า EC ใดพบ Defect ให้แก้ในรอบเดิมแล้ว QA ซ้ำ **ไม่แตกเป็น EC ใหม่**
- ถ้า EC ใดใหญ่เกินจนตรวจ Diff ไม่ไหวจริง ให้ Codex รายงาน `BLOCKED — NEEDS DECISION` ก่อน ห้ามแตกเอง

---

## 7. Updated Day 0–7 Timeline

### Day 0 — Environment & Planning Gate (ไม่นับเป็นวันพัฒนา)

| ลำดับ | งาน | ผู้รับผิดชอบ | Permission |
|---|---|---|---|
| 0.1 | Master Plan + Revision | Claude | Read-only |
| 0.2 | ตรวจและอนุมัติแผน | ChatGPT + ผู้ใช้ | — |
| 0.3 | **Read-only Environment Audit** | Codex | Read-only |
| 0.4 | **Environment Repair** (ถ้าจำเป็น) | ผู้ใช้ตามคำสั่งที่ Codex ระบุ | ผู้ใช้รันเอง |
| 0.5 | **Re-audit จนผ่าน** | Codex | Read-only |

**Gate:** ต้องได้ `READY TO START DAY 1` เท่านั้น

**กฎเวลาที่เพิ่มใหม่:**
1. **Day 1 เริ่มนับหลังได้ `READY TO START DAY 1`** ไม่ใช่ตามวันปฏิทิน
2. งานติดตั้ง PHP Extension, MySQL, Composer, Node, สิทธิ์โฟลเดอร์ ทั้งหมดอยู่ใน Day 0
3. หากพบปัญหา Environment **หลัง** เริ่ม Day 1 แล้ว (เช่น Extension ขาดตอนติดตั้ง Package) Codex ต้องรายงานเป็นรายการแยกพร้อมเวลาที่เสียไป **ห้ามกลืนเข้าไปในเวลาพัฒนาเงียบ ๆ**
4. ถ้าเวลาซ่อม Environment เกินครึ่งวัน ต้องแจ้งผู้ใช้เพื่อพิจารณาเลื่อนเส้นตายหรือเรียกใช้รายการ Deferrable ตาม §29 เดิม

### Day 1–7

| วัน | EC | โฟกัส | Working Increment | Overall |
|---|---|---|---|---|
| **1** | EC-1, EC-2 | Foundation · Database · Auth · Access · App Shell | Login 2 Role เข้าพื้นที่ถูกต้อง · Suspended เห็นหน้าแจ้งเหตุผล · Schema พร้อม | 25% |
| **2** | EC-3, EC-4 | Profile · Image + Compensation · Skills | ตั้งค่าโปรไฟล์ จัดการรูป และจัดการทักษะได้ครบ | 45% |
| **3** | EC-5, EC-6 | Search · Public Profile · Create Request · Listing | ค้นหาคนอื่นและส่งคำขอ pending ได้ · กันซ้ำตาม SD-6 | 65% |
| **4** | EC-7, EC-8 | State Transition · Observer · Queue · Notification | Flow ครบต้นจนจบ · แจ้งเตือนเข้าคิวหลัง Commit | 82% |
| **5** | EC-9, EC-10 | Admin ทั้งหมด · Demo Data · Route Matrix Audit | Student และ Admin ครบตาม Scope · ไม่มี Module หลักค้าง | 93% |
| **6** | EC-11 | Regression · Defect Repair (ห้ามเพิ่ม Feature) | ระบบเสถียร Critical/High = 0 | 98% |
| **7** | EC-12 | Production · Submission | สาธิตและส่งได้จริง | 100% |

**หลักที่คงเดิม:** ทุกวันมี Working Increment · Test อยู่ในรอบเดียวกับ Feature · UX/UI ทำพร้อม Module · Day 7 ไม่มี Core Module ที่ยังไม่เริ่ม

---

## 8. Updated Completion Gates

### 8.1 Gate ระดับ Execution Checkpoint

ทุก EC ต้องผ่านครบทั้ง 5 ข้อจึงจะ Commit ได้:

| # | เกณฑ์ |
|---|---|
| G-1 | Automated Test ในขอบเขตผ่านทั้งหมด และ Suite เดิมไม่พัง |
| G-2 | Manual Browser QA ที่ระบุใน EC นั้นผ่าน และผู้ใช้อนุมัติ |
| G-3 | `git diff --stat` ตรงกับ Boundary ที่ประกาศ ไม่มีไฟล์นอกขอบเขต |
| G-4 | ไม่มีงานของ EC ถัดไปปรากฏใน Diff |
| G-5 | Standing UX Checklist 7 ข้อ (§22 เดิม) ผ่าน |

### 8.2 Gate ระดับวัน

| วัน | Automated | Manual QA | Structural | Scope Guard |
|---|---|---|---|---|
| **0** | — | ผู้ใช้ยืนยันผลคำสั่งจริง | Lock Files ครบและ SHA256 ตรงฝั่ง Repository | ได้ `READY TO START DAY 1` · ยังไม่มีโค้ด |
| **1** | Auth/Access ผ่าน | Login 2 Role · Suspended เห็นหน้าแจ้งเหตุผลและ Logout POST ได้ · ไม่มี Redirect Loop | `migrate:fresh` ผ่านทั้ง dev และ test · Index/Unique/FK ครบตาม Contract | Sidebar มีเฉพาะเมนูที่คลิกได้ · ไม่มีไฟล์ Day 2 |
| **2** | Profile/Skills ผ่าน รวม IM-1…IM-5 | Replace แล้วไฟล์เก่าหายจริงบน Disk · DB ไม่เคยชี้ไฟล์ที่ไม่มีอยู่ | `ProfileImageService` แยกจาก Controller · Compensation มีอยู่จริงในโค้ด | ไม่มีไฟล์ Day 3 |
| **3** | Search/Create ผ่าน รวมเคส SD-6 | ส่งซ้ำถูกกัน · ทิศทางกลับกันและคู่ทักษะต่างกันส่งได้ | Duplicate Guard อยู่ใน Transaction พร้อม `lockForUpdate` · ไม่มี Partial Unique Index | ไม่มีปุ่ม Action ของ Day 4 โผล่ |
| **4** | Transition 16 เคส + Observer 11 เคส ผ่าน | เปิด Worker แล้วแจ้งเตือนเข้าจริง · `failed_jobs` ว่าง | Observer ใช้ `wasChanged`/`getOriginal` · Dispatch หลัง Commit · ไม่มี Mail Blocking | Observer ไม่มี Business Logic |
| **5** | Admin ผ่าน + Route/Middleware Assertion เต็ม | Suspend มีผลข้าม Session จริง · Monitor ไม่มีปุ่มเปลี่ยนสถานะ | **ตรวจ Method + URI + Middleware + Access ครบ 39 แถว** · Route ส่วนเกินมีเฉพาะที่ประกาศไว้ | Instructor Requirements ครบ 10 ข้อ · ไม่มี Route ตาม SD-2/SD-3/SD-4 |
| **6** | `php artisan test` ทั้งชุดผ่าน | Full User Flow ผ่าน | ไม่มี Dead Code · ไม่มี Debug Dump · ไม่มี Secret · คอมเมนไทยครบและตรงกับโค้ด | Critical/High Defect = 0 · ไม่มี Feature ใหม่ |
| **7** | Suite ผ่านอีกครั้งหลัง Cache/Optimize | Final Demo Run ผ่าน | `.env` ไม่ถูก Commit · `git status` สะอาด | Final Acceptance Checklist ผ่านครบ |

### 8.3 Gate ที่ถูกยกเลิก

| Gate เดิม | สถานะ | เหตุผล |
|---|---|---|
| "Route ตรง Matrix 36 เส้น" | **ยกเลิก** | ตัวเลขผิด และการนับไม่พิสูจน์ Middleware หรือ Access |
| "หนึ่ง Commit ต่อ Checkpoint" ที่ระดับ 20 Checkpoint | **แทนที่** | เปลี่ยนเป็น 12 Execution Checkpoints ตาม Natural Boundary |
| "DB สำเร็จก่อน แล้วจึงแตะ Disk" ในฐานะกฎเดียว | **แทนที่** | ไม่ครอบคลุมกรณี Store สำเร็จแต่ DB ล้ม ต้องมี Compensation |

---

## 9. Final Recommended First Codex Boundary

### EC-0 — Environment Audit
**Permission: Read-only เท่านั้น**
**อ้างอิง:** `prompts/01-CODEX-READ-ONLY-AUDIT.md`

รอบแรกไม่ใช่การติดตั้ง Laravel และไม่ใช่การซ่อม Environment — เป็นการตรวจและรายงานเท่านั้น

**ขอบเขตที่ต้องตรวจและรายงาน:**

| # | รายการ | สิ่งที่ต้องได้ |
|---|---|---|
| 1 | `php -v`, `where php` | เวอร์ชันและ Path ที่ใช้จริง |
| 2 | PHP Extensions | `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `tokenizer`, `xml`, `ctype`, `curl` |
| 3 | `composer --version`, `node -v`, `npm -v`, `git --version` | เวอร์ชันจริง |
| 4 | Repository Path `C:\xampp\htdocs\student-skill-exchange` | มีอยู่หรือยัง · `git status` |
| 5 | Lock Files | อยู่ใน `docs/` และ `prompts/` ตาม Manifest จริง · SHA256 ตรงทั้ง 14 ไฟล์ฝั่ง Repository |
| 6 | MySQL/MariaDB | เวอร์ชัน · สิทธิ์สร้าง `student_skill_exchange` และ `student_skill_exchange_test` · Default Engine เป็น InnoDB |
| 7 | สิทธิ์เขียน | `storage/`, `bootstrap/cache/` |
| 8 | Laravel เดิม | ยืนยันว่าไม่มีโปรเจกต์ค้างอยู่ |
| 9 | Mail/Queue Readiness | ยืนยันว่าใช้ Driver ตาม A-4 และ A-5 ได้ |

**ข้อห้ามในรอบนี้:** ห้ามติดตั้ง · ห้ามสร้างไฟล์ · ห้ามสร้าง Laravel Project · ห้ามรัน Migration · ห้ามสร้างฐานข้อมูล · ห้าม Commit/Push · ห้ามเริ่ม Day 1 · ห้ามเดาข้อมูลที่ตรวจไม่ได้

**สถานะที่ต้องได้กลับ:** `READY TO START DAY 1` หรือ `BLOCKED — ENVIRONMENT NOT READY`

**หากได้ `BLOCKED — ENVIRONMENT NOT READY`:**
1. Codex ระบุคำสั่งซ่อมที่แน่นอน ผู้ใช้เป็นผู้รันเอง
2. Codex ทำ Re-audit
3. วนจนได้ `READY TO START DAY 1`
4. **ทั้งหมดนี้ยังอยู่ใน Day 0 นาฬิกา Day 1 ยังไม่เริ่มเดิน**

**Boundary ถัดไปหลังผ่าน Gate:** EC-1 Foundation & Database Contract (Manual Implementation) — ติดตั้ง Laravel, ตั้งค่า `.env`/`.env.testing`, `storage:link`, locale/timezone, Migration 8 ตารางตามลำดับ §12.1, Model + Relationship + Factory + Seeder แล้วหยุดรายงานเพื่อรอ Manual QA

---

**สิ่งที่รอจากผู้ใช้:** อนุมัติเอกสารฉบับแก้ไขนี้ แล้วจึงให้ ChatGPT จัดทำ Prompt สำหรับ EC-0 — Claude ไม่จัดทำ Prompt ให้ Codex ตาม `09-WORKFLOW-GOVERNANCE §1`

---

REVISED 7-DAY CODEX PLAN READY FOR FINAL APPROVAL
