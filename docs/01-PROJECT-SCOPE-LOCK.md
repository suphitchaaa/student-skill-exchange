# 01 — Project Scope Lock

## Project

ชื่อระบบ: ระบบแลกเปลี่ยนทักษะระหว่างนักศึกษา  
English: Student Skill Exchange

## Roles

### Student

- Register, Login, Logout
- ดูและแก้ไขโปรไฟล์ตนเอง
- Upload / Replace / Delete รูปโปรไฟล์
- CRUD ทักษะที่สอนได้
- CRUD ทักษะที่ต้องการเรียน
- ค้นหาทักษะที่มีอยู่หรือเพิ่มชื่อทักษะใหม่เป็นทักษะของตนเองได้ทันทีโดยไม่รอ Admin อนุมัติ
- ค้นหานักศึกษาด้วยชื่อ ทักษะ คณะ ชั้นปี
- ดู Public Student Profile
- ส่งคำขอแลกเปลี่ยน
- ดูคำขอที่ส่ง/ได้รับ/ประวัติ
- Accept / Reject คำขอที่ได้รับ
- Cancel คำขอที่ส่งและยัง Pending
- Complete กิจกรรมที่ Accepted

### Admin

- Login หลังบ้าน
- Dashboard พื้นฐาน
- ดู ค้นหา Suspend และ Activate นักศึกษา
- CRUD รายการทักษะส่วนกลาง
- ดู ค้นหา กรองคำขอทั้งหมด

Admin ห้าม Accept หรือ Reject แทนนักศึกษา

## Instructor Requirements

1. ผู้ใช้อย่างน้อย 2 กลุ่ม
2. Middleware
3. Relational Database
4. CRUD
5. Upload Image
6. Replace/Delete Image แล้วลบไฟล์เก่าจริง
7. Asynchronous Email ผ่าน Queue
8. Observer
9. Feature Tests
10. Production Configuration

## In Scope

- Authentication
- Student/Admin Role
- Role Middleware
- Account Active/Suspended Guard
- Student Profile
- Profile Image Management
- Offered/Wanted Skill CRUD
- Student Skill Search/Add ลงรายการทักษะส่วนกลาง โดยทักษะใหม่ใช้งานได้ทันที
- Student Search
- Public Student Profile
- Exchange Request Workflow
- Queue Email
- Database Notification
- ExchangeRequestObserver
- Admin Student Management
- Admin Skill Management
- Admin Request Monitoring
- Feature Tests
- Production Setup
- Bootstrap Responsive UX/UI

## Out of Scope

- Chat
- Real-time Notification / WebSocket
- Video Call
- AI Matching / Recommendation
- Review / Rating
- Point / Badge / Gamification
- Payment
- Social Login
- Complex Calendar
- Map / GPS
- Advanced Report / Chart
- Export PDF
- Mobile Application
- Multi-language
- Product System
- Marketplace
- Booking
- Dead Button/Menu/Route/Page

## Change Control

Claude, Codex และ ChatGPT ห้ามตัดหรือเพิ่ม Scope เอง  
หากต้องลดรายละเอียด ต้องเสนอผลกระทบและรอผู้ใช้อนุมัติ
