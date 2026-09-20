# DESIGN.md — Student Skill Exchange

## 1. Design Direction

`MotionSite-inspired University Dashboard`

ระบบมหาวิทยาลัยสมัยใหม่ที่ให้ความรู้สึก:

- Premium และร่วมสมัย
- มีลำดับชั้นของข้อมูลที่ชัดและมี contrast สูง
- ใช้ composition แบบ editorial ที่ตั้งใจออกแบบ
- Dynamic อย่างมีวินัย และใช้ motion เฉพาะเมื่อช่วยการรับรู้
- เป็นระบบมหาวิทยาลัยที่มีโครงสร้าง ใช้งานง่าย และออกแบบโดยมนุษย์
- Student UI แสดงบุคลิกได้มากกว่าโดยไม่กลายเป็น Social Media หรือ AI SaaS
- Admin UI สงบ กระชับ และเน้นการอ่านข้อมูล

ใช้แนวทางอ้างอิงจาก MotionSite ในระดับ mood, hierarchy และ composition เท่านั้น
ห้ามคัดลอก brand, layout, copywriting, feature หรือ source code แบบ 1:1

UX/UI รอบนี้เป็นการปรับ Presentation เท่านั้น

ห้ามเปลี่ยน:
- Business Logic
- Database Contract
- Migration
- Route
- Middleware / Authorization
- Exchange Request State Flow
- Queue / Observer behavior
- Scope Lock
- Feature เดิม

---

## 2. Governance Priority

ลำดับกฎที่ต้องยึด:

1. Approved Scope / Governance Documents
2. `docs/04-UX-UI-RULES.md`
3. Approved User Flow / Route / Database Contract
4. `DESIGN.md` ฉบับนี้
5. Reference Images

Reference ใช้เพื่อกำหนด:
- Mood
- Layout
- Visual hierarchy
- Card style
- Spacing
- Color usage
- Component feeling

ห้าม Copy:
- Brand
- Logo
- Copywriting
- Feature
- Source Code
- Content
- Layout แบบ 1:1

---

## 3. Visual Reference Direction

### Primary Direction

ใช้แนวคิด:

- Left Sidebar + Main Content
- Desktop Dashboard ที่มีโครงสร้างชัด
- Compact welcome section
- Structured surfaces with controlled radius
- Warm neutral surfaces with restrained pastel accents
- Strong information hierarchy
- Profile Card ที่อ่านง่าย
- Summary Cards ที่แยกข้อมูลเป็นหมวด
- List / Activity / Request Card ที่สแกนข้อมูลได้เร็ว

### Secondary Direction

ใช้แนวคิด:

- Editorial spacing and deliberate alignment
- High-contrast typography for page and data hierarchy
- Soft pastel accents used to support real content and status
- Controlled borders and restrained shadows
- Motion only when it clarifies an existing interaction; no decorative animation and no new JavaScript behavior

### Student UI

อนุญาตให้:
- expressive กว่าในระดับ composition และ accent
- ใช้ pastel surface เพื่อแยกข้อมูลจริงได้มากกว่า
- ใช้ card ที่มีบุคลิกชัด แต่ยังเป็น information component

### Admin UI

ต้อง:
- ใช้ Design System เดียวกัน
- ลด illustration
- ลด decorative element
- เน้น Table / Form / Data readability
- ใช้สี accent เท่าที่จำเป็น
- อ่านสถานะและ action ได้ชัด

---

## 4. Core Color Palette

### Brand / Reference Colors

| Token | HEX | Usage |
|---|---|---|
| Warm Ivory | `#F8F5EF` | Main application background |
| Soft White | `#FFFDFC` | Primary card and control surface |
| Deep Ink | `#221E1F` | Main text and high-contrast hierarchy |
| Reddish Brown | `#8B4C41` | Deep accent, active state, and strong anchor |
| Petal Rose | `#D96F89` | Primary action and restrained highlight |
| Blush | `#F7E5EA` | Student accent surface |
| Muted Sage | `#A3B9AE` | Secondary accent and muted UI |
| Pale Sage | `#E8F0EC` | Soft data and status surface |
| Soft Lavender | `#D8D0EE` | Limited summary and completed-status surface |
| Butter | `#F1D57A` | Pending-status and limited highlight surface |
| Warm Grey | `#746E6B` | Secondary text |
| Warm Border | `#E6DED8` | Borders and dividers |

### Supporting Colors

ใช้ได้เฉพาะในปริมาณจำกัด:

| Token | Suggested HEX | Usage |
|---|---|---|
| Blush | `#F7E5EA` | Student accent surface / soft tag |
| Pale Sage | `#E8F0EC` | Soft data surface / accepted status |
| Soft Lavender | `#D8D0EE` | Summary card / completed status |
| Butter | `#F1D57A` | Pending status / limited highlight |

ใช้สีใน palette ที่ล็อกไว้เท่านั้น

---

## 5. Semantic Tokens

ควรกำหนด CSS Variables กลางแทน hard-code สีซ้ำ

ตัวอย่าง:

```css
:root {
    --color-bg: #F8F5EF;
    --color-surface: #FFFDFC;
    --color-surface-soft: #F7E5EA;

    --color-primary: #D96F89;
    --color-primary-hover: #C75D78;

    --color-secondary: #A3B9AE;
    --color-accent-dark: #8B4C41;

    --color-text: #221E1F;
    --color-text-muted: #746E6B;

    --color-border: #E6DED8;

    --color-lavender: #D8D0EE;
    --color-yellow: #F1D57A;
}
```

---

## 6. Color Usage Rules

### Background

Main application background:

```text
Warm Ivory
#F8F5EF
```

หรือ white / off-white ตามบริบท

### Card

หลัก:

```text
#FFFDFC
```

ใช้ Blush / Pale Sage / Lavender / Butter เป็น card accent ได้บางใบ โดยต้องผูกกับข้อมูลจริง

### Primary Action

ใช้:

```text
Petal Rose
#D96F89
```

### Deep Accent

ใช้:

```text
Reddish Brown
#8B4C41
```

เหมาะกับ:
- Heading บางจุด
- Active navigation
- Strong text
- Important visual anchor

### Muted Sage

ใช้กับ:
- Secondary information surfaces
- Icon background
- Muted tags
- Decorative UI

---

## 7. Typography

เป้าหมาย:

- อ่านง่าย
- ดูร่วมสมัย
- มีลำดับชั้นที่ชัด
- ไม่เหมือน AI landing page
- ไม่ใช้ decorative font ใน body

### Font Family

ใช้ `Anuphan` สำหรับระบบทั้งหมด โดยใช้ weights `400 / 500 / 600 / 700`

### Heading

ใช้ font weight:

```text
600–700
```

### Body

ใช้:

```text
400–500
```

### Hierarchy

แนะนำ:

```text
Page Title: 24–28px
Section Title: 18–22px
Card Title: 15–18px
Body: 14–16px
Helper / Meta: 12–14px
```

ต้องรองรับภาษาไทยได้ดี

ห้าม:
- font handwritten
- comic font
- display font ที่อ่านยาก
- typography ที่ดูเป็นโปสเตอร์มากกว่าระบบ

---

## 8. Spacing System

ใช้ spacing ที่สม่ำเสมอ:

```text
4px
8px
12px
16px
20px
24px
32px
40px
48px
```

แนวทาง:

- Card padding: 20–24px
- Section gap: 24–32px
- Form field gap: 16–20px
- Button internal spacing: 10–16px
- Page horizontal padding: 24–32px

ห้ามยัดข้อมูลแน่นเกินไป

---

## 9. Border Radius

Design ใหม่ใช้ความโค้งมากขึ้น

แนะนำ:

```text
Small Control: 8–10px
Input: 10–12px
Button: 10–12px
Card: 14–18px
Compact welcome surface: 18–20px
Badge / Pill: 999px
```

ใช้ radius แบบ pill เฉพาะ status badge หรือ tag ที่มีความหมายจริง ห้ามใช้กับ generic UI element

ห้ามทุกอย่างกลมจนดูเป็น Mobile App ทั้งระบบหรือ pill ทุกองค์ประกอบ

---

## 10. Shadows

ใช้ shadow เบาเท่านั้น

ตัวอย่าง:

```css
box-shadow: 0 8px 24px rgba(80, 60, 60, 0.06);
```

หรือ:

```css
box-shadow: 0 4px 14px rgba(80, 60, 60, 0.05);
```

ห้าม:
- Glow
- Neon Shadow
- Shadow เข้ม
- Floating effect หนัก

---

## 11. Gradient / Effects

Default:

- ไม่ใช้ Gradient
- ไม่ใช้ Glassmorphism
- ไม่ใช้ Glow
- ไม่ใช้ Neon
- ไม่ใช้ Blur Panel

`docs/04-UX-UI-RULES.md` ห้าม gradient และมีลำดับสูงกว่า DESIGN.md

---

## 12. App Shell

ระบบใช้:

```text
Left Sidebar
+
Main Content
+
Top Area / Header
```

### Sidebar

เป้าหมาย:

- ดูทันสมัย
- อ่านเมนูง่าย
- Active state ชัด
- ไม่หนาหนักเกินไป

แนะนำ:

- Width ประมาณ 230–260px
- Logo/System Name ด้านบน
- Menu icon + label
- Active item มี rounded background
- Menu spacing โปร่ง
- Logout แยกออกจาก navigation หลัก

Student Sidebar สามารถใช้ accent ที่เป็นมิตรมากกว่า Admin

### Top Area

ควรมี:

- Page Title
- Supporting text เมื่อจำเป็น
- User identity
- Notification หาก Scope เดิมมีอยู่

ห้ามเพิ่ม Search / Notification / Action ใหม่ถ้าไม่มี Feature จริงรองรับ

---

## 13. Dashboard

Student Dashboard ควรเป็นหน้าที่มีบุคลิกมากที่สุด

Structure แนะนำ:

```text
Compact welcome surface
↓
Summary Cards
↓
Recent Exchange / Activity
↓
Relevant Skills / Requests
```

### Welcome Card

สามารถใช้:
- Warm soft background with clear text contrast
- Greeting
- Supporting text

ห้าม oversized hero, decorative content ที่ไม่มีข้อมูลจริง หรือ CTA ที่ไม่มี feature รองรับ

### Summary Cards

แนะนำ 3–4 ใบ

แต่ละใบควรมี:
- Icon
- Label
- Value
- Soft accent background

สามารถใช้:
- Blush
- Pale Sage
- Soft Lavender
- Butter

แต่ต้องยังอ่าน text ได้ชัด

---

## 14. Profile

Profile Page สามารถอ้างอิง structure แบบ:

```text
Large Profile Card

[Profile Image] [Student Information]
                [Edit Action]
                [Meta Information]
                [Skills / Related Info]
```

เป้าหมาย:
- Profile image เด่นแต่ไม่ใหญ่เกิน
- ข้อมูลแบ่งเป็นบรรทัด
- icon ช่วย scan
- Edit action หาเจอง่าย
- ไม่มี social links หรือข้อมูลนอก Scope

---

## 15. Skills

Skills UI ต้องทำให้ Offered / Wanted แตกต่างกันชัด

ใช้:
- Tabs
- Card
- Tags
- Compact actions

### Offered

แนะนำโทน:

```text
Pale Sage / Muted Sage
```

### Wanted

แนะนำโทน:

```text
Blush / Soft Lavender
```

Skill tag ต้อง:
- อ่านง่าย
- ไม่ใช้สีเข้มหลายสีพร้อมกัน
- ไม่ใช้ gradient
- ไม่ใส่ decoration เกินจำเป็น

---

## 16. Search and Student Cards

Student Search ต้องเป็นหน้าที่ scan ได้เร็ว

Student Card ควรประกอบด้วย:

- Profile Image
- Name
- Faculty / Major / Year
- Offered Skills
- Action ตาม Scope เดิม

ใช้ card surface สีขาว มี border บาง และ shadow เบา

สามารถมี pastel accent ที่ tag หรือ icon เท่านั้น

ห้ามทำ card ซ้อนเอียงแบบ decorative reference ใน UI จริง เพราะจะลด usability

Reference card ซ้อนใช้เป็น inspiration ด้าน:
- สี
- softness
- radius
- badge

เท่านั้น

---

## 17. Public Profile

ใช้ design language เดียวกับ Profile

เน้น:
- Identity
- Faculty / Major / Year
- Offered Skills
- Wanted Skills
- Exchange Action ตามสิทธิ์เดิม

ห้ามเพิ่มข้อมูล social media หรือ feature ใหม่

---

## 18. Exchange Requests

Request UI ต้องให้สถานะอ่านง่ายทันที

ใช้:
- Status badge
- Clear student identity
- Offered / Requested skill
- Schedule
- Learning format
- Action buttons ตาม Policy เดิม

Status colors ต้อง consistent ทุกหน้า

ตัวอย่าง semantic:

```text
Pending    → Butter
Accepted   → Pale Sage
Rejected   → Blush / red semantic
Cancelled  → Warm Grey / Neutral
Completed  → Soft Lavender
```

ห้ามใช้สีอย่างเดียวเป็นตัวบอกสถานะ ต้องมี text label เสมอ

---

## 19. Buttons

### Primary

Petal Rose

```text
#D96F89
```

### Secondary

White / Soft surface + Border

### Destructive

ใช้ red semantic ที่อ่านชัด ไม่ใช้ Petal Rose แทน destructive ทุกกรณี

### Button Rules

- Radius 10–12px
- Icon + text เมื่อช่วยความเข้าใจ
- icon ต้องเป็น Bootstrap Icons
- Action สำคัญต้องเห็นชัด
- ห้ามมี Dead Button
- ห้ามตกแต่งจนดูเหมือน Sticker

---

## 20. Forms

Form ต้อง:

- Label ชัด
- Input height สม่ำเสมอ
- Helper text อ่านง่าย
- Validation message ใกล้ field
- Required state ชัด
- Error ไม่ใช้สีอย่างเดียว

Input Style:

```text
White background
Thin border
10–12px radius
Clear focus state
```

Focus สามารถใช้ Petal Rose หรือ Muted Sage tint

---

## 21. Tables

Admin Table ต้องเน้น readability

ใช้:
- Header background อ่อน
- Row spacing พอดี
- Border บาง
- Status badge
- Action buttons compact
- Table container มี rounded card

ห้าม:
- Rainbow row
- Decoration หลังข้อความ
- Illustration ใน table
- shadow ทุก row

ที่ 1366×768 ห้ามเกิด horizontal scroll ทั้งหน้า

ถ้าจำเป็น Table สามารถ scroll ภายใน container ได้ตาม Approved UX/UI Rules

---

## 22. Admin Dashboard

ใช้ Design System เดียวกับ Student แต่:

- Illustration น้อยกว่า
- สี pastel น้อยกว่า
- Summary card อ่านง่าย
- Recent requests เน้นข้อมูล
- Admin action ชัดเจน
- ไม่มี chart ปลอม
- ไม่เพิ่ม graph ถ้าไม่มีข้อมูลหรือ Scope รองรับ

---

## 23. Icons

ใช้:

```text
Bootstrap Icons
```

ชุดเดียว

Rules:

- visual weight สม่ำเสมอ
- icon size สอดคล้อง context
- icon background สามารถใช้ pastel circle/square
- icon ไม่ใช่ decoration อย่างเดียวเมื่อไม่จำเป็น

ห้าม:
- Emoji
- mixed icon libraries
- AI sparkle icons
- robot icons
- unrelated decorative icons

---

## 24. Illustration

อนุญาต:

- Simple flat illustration
- Simple campus / learning / exchange concepts
- Geometric characters
- Abstract academic shape
- Plants / books / learning objects แบบเรียบง่าย

ใช้ได้ใน:

- Empty State
- Onboarding-like empty section
- เนื้อหาประกอบที่มีอยู่จริงและไม่ใช่ hero decoration

Student Dashboard ต้องใช้ compact functional welcome surface ที่นำด้วยลำดับชั้นข้อมูล
ห้าม decorative hero illustration หรือ oversized hero

ห้าม:

- 3D render
- AI robot
- futuristic hologram
- neon
- glass
- realistic stock person ที่ทำให้ UI ดูเป็น template
- illustration ที่ใหญ่กว่าข้อมูลหลัก

Admin ต้องใช้ illustration อย่างจำกัด

---

## 25. Decorative Shapes

ใช้ structural composition แทน decorative floating shapes:

- spacing และ alignment ที่ตั้งใจ
- typography hierarchy
- border และ surface ที่มีโครงสร้าง
- intentional color blocks ที่ผูกกับเนื้อหาจริง

ห้ามใช้ starburst, organic shape, blob หรือ sparkle เป็น decoration

---

## 26. Empty States

ทุก empty state ต้องมี:

1. Title
2. Short explanation
3. Action เมื่อมี action จริง
4. Illustration/Icon เล็กได้

Tone:
- เป็นมิตร
- กระชับ
- ไม่ตลกเกินไป
- ไม่ใช้ Emoji

---

## 27. Notifications / Status Feedback

ใช้:
- Toast / Alert / Inline feedback ตามโครงสร้างเดิม
- สี semantic ชัด
- text กระชับ
- icon Bootstrap

Success / Warning / Error ต้องใช้รูปแบบสม่ำเสมอทั้งระบบ

---

## 28. Responsive Rules

Primary QA viewport:

```text
1366 × 768
```

ต้อง:

- ไม่มี Horizontal Scroll ทั้งหน้า
- Sidebar ไม่กินพื้นที่มากเกิน
- Main Content อ่านได้
- Card ไม่ล้น
- Table จัดการ overflow ภายใน component
- Form ไม่แคบเกิน

Responsive ต้องไม่ทำลาย:
- Information hierarchy
- Action visibility
- Status readability

---

## 29. Accessibility

ต้องรักษา:

- Contrast อ่านได้
- Focus state เห็นชัด
- Label ทุก form
- Button มีข้อความหรือ accessible label
- Status ไม่ใช้สีอย่างเดียว
- Click target ไม่เล็กเกินไป
- Text ไม่เล็กจนอ่านยาก

Pastel color ต้องไม่ถูกใช้เป็น text color หาก contrast ไม่พอ

---

## 30. Student vs Admin Personality

### Student

```text
Expressive
Warm
Editorial
Dynamic อย่างมีวินัย
Pastel accents
Structured
Student-centered
```

### Admin

```text
Clean
Structured
Calm
Data-focused
Consistent
Professional
```

ทั้งสองฝั่งต้องรู้ว่าเป็นระบบเดียวกัน

---

## 31. Forbidden Visual Style

ห้าม:

- Generic AI SaaS Template
- AI dashboard look
- Robot
- AI Sparkle
- Excessive Purple SaaS style
- Glassmorphism
- Glow
- Neon
- 3D illustration
- Floating random blobs
- Gradient backgrounds
- Emoji
- Mixed icon libraries
- Excessive animation
- Over-decorated cards
- Mobile-app look บน Desktop ทุกหน้า
- Over-rounded everything
- Fake analytics
- Fake charts
- Decorative content ที่ไม่มีข้อมูลจริง

---

## 32. UX Behavior Rules

UI ต้องช่วยให้ผู้ใช้เข้าใจ:

```text
Where am I?
What can I do?
What happens next?
What is the current status?
```

Action สำคัญต้อง:
- หาเจอง่าย
- label ตรงความหมาย
- ไม่ซ่อนโดยไม่จำเป็น

Destructive Action ต้อง:
- ชัดเจน
- confirmation ตาม behavior เดิม

---

## 33. Implementation Boundary

UX/UI Refinement สามารถแก้:

```text
resources/views/**
resources/css/**
resources/js/**
```

เฉพาะ presentation-related behavior

หลีกเลี่ยงการแก้:

```text
app/**
routes/**
database/**
config/**
tests/**
```

เว้นแต่มีเหตุผลที่ผ่าน review และไม่ใช่การเพิ่ม Feature

ห้ามเปลี่ยน:

- Database schema
- Route contract
- Policies
- Middleware
- Validation contract
- Queue
- Observer
- Exchange state transition
- Business logic

---

## 34. Development Approach

ลำดับ UX/UI Refinement:

### UXUI-0
Visual Audit

### UXUI-1
App Shell / Global Design System

### UXUI-2
Student Pages

### UXUI-3
Admin Pages

### UXUI-4
Responsive / Empty / Validation / Polish

### UXUI-5
Regression / Final Review

ห้ามแก้หลาย domain พร้อมกันจน Diff ตรวจไม่ได้

---

## 35. UXUI-0 Audit Areas

ต้องตรวจ:

1. App Shell
2. Sidebar
3. Top Area
4. Student Dashboard
5. Profile
6. Skills
7. Search
8. Public Profile
9. Exchange Requests
10. Admin Dashboard
11. Student Management
12. Skill Management
13. Request Monitoring
14. Forms
15. Tables
16. Empty States
17. Validation
18. Responsive 1366×768

---

## 36. Visual QA Checklist

ก่อนถือว่าหน้าใดผ่าน ต้องตรวจ:

- [ ] Information hierarchy ชัด
- [ ] Spacing สม่ำเสมอ
- [ ] Typography สม่ำเสมอ
- [ ] Card radius สม่ำเสมอ
- [ ] Button hierarchy ถูก
- [ ] Icon ใช้ Bootstrap Icons
- [ ] ไม่มี Emoji
- [ ] ไม่มี Generic AI decoration
- [ ] Status อ่านง่าย
- [ ] Form label/validation ครบ
- [ ] Empty state ถูกต้อง
- [ ] ไม่มี Dead UI
- [ ] ไม่มี Horizontal Scroll ทั้งหน้า @ 1366×768
- [ ] Student/Admin ดูเป็น Design System เดียวกัน
- [ ] Functionality เดิมไม่เปลี่ยน
- [ ] Scope Lock ไม่ถูกละเมิด

---

## 37. Final Direction Summary

Student Skill Exchange หลัง UX/UI Refinement ต้องมีภาพรวมว่า:

> ระบบมหาวิทยาลัยที่ทันสมัย มีลำดับชั้นข้อมูลชัด และใช้งานง่าย
> ใช้ warm neutral palette และ pastel accent อย่างมีวินัย
> มี editorial composition ที่มีบุคลิกในฝั่งนักศึกษา
> แต่ยังมีโครงสร้างข้อมูลที่น่าเชื่อถือ
> Admin อ่านข้อมูลและทำงานได้รวดเร็วในบรรยากาศที่สงบ
> ทุกหน้าดูเป็นระบบเดียวกัน
> และไม่ดูเหมือน Generic AI SaaS Template

Primary visual identity:

```text
MotionSite-inspired University Dashboard
Premium editorial composition
Strong information hierarchy
Warm contrast
Structured university UX
Student-centered
```

Palette:

```text
Warm Ivory     #F8F5EF
Soft White     #FFFDFC
Deep Ink       #221E1F
Reddish Brown  #8B4C41
Petal Rose     #D96F89
Blush          #F7E5EA
Muted Sage     #A3B9AE
Pale Sage      #E8F0EC
Soft Lavender  #D8D0EE
Butter         #F1D57A
Warm Grey      #746E6B
Warm Border    #E6DED8
```

UX/UI Refinement must improve presentation without changing the completed EC-12 functional baseline.
