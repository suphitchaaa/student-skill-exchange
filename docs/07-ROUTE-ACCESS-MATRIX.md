# 07 — Route Access Matrix

## Public/Auth

| Method | URI | Access |
|---|---|---|
| GET | / | Public |
| GET | /login | Guest |
| POST | /login | Guest |
| GET | /register | Guest |
| POST | /register | Guest |
| POST | /logout | Auth |

## Suspended

| Method | URI | Access |
|---|---|---|
| GET | /account/suspended | Auth suspended/active safe route |

## Student

Middleware: `auth`, `role:student`, `account.active`

| Method | URI |
|---|---|
| GET | /dashboard |
| GET | /profile |
| GET | /profile/edit |
| PUT/PATCH | /profile |
| POST | /profile/image |
| DELETE | /profile/image |
| GET | /my-skills |
| POST | /my-skills |
| PUT/PATCH | /my-skills/{userSkill} |
| DELETE | /my-skills/{userSkill} |
| GET | /students |
| GET | /students/{user} |
| GET | /students/{user}/exchange-request |
| POST | /students/{user}/exchange-request |
| GET | /exchange-requests |
| GET | /exchange-requests/{exchangeRequest} |
| PATCH | /exchange-requests/{exchangeRequest}/accept |
| PATCH | /exchange-requests/{exchangeRequest}/reject |
| PATCH | /exchange-requests/{exchangeRequest}/cancel |
| PATCH | /exchange-requests/{exchangeRequest}/complete |

## Admin

Middleware: `auth`, `role:admin`, `account.active`

| Method | URI |
|---|---|
| GET | /admin/dashboard |
| GET | /admin/students |
| GET | /admin/students/{user} |
| PATCH | /admin/students/{user}/suspend |
| PATCH | /admin/students/{user}/activate |
| GET | /admin/skills |
| GET | /admin/skills/create |
| POST | /admin/skills |
| GET | /admin/skills/{skill}/edit |
| PUT/PATCH | /admin/skills/{skill} |
| DELETE | /admin/skills/{skill} |
| GET | /admin/exchange-requests |
| GET | /admin/exchange-requests/{exchangeRequest} |

## HTTP Rules

- Create = POST
- Update/Transition = PUT/PATCH
- Delete = DELETE
- Logout = POST
- ทุก State-changing Request ใช้ CSRF
- ห้าม State Change ด้วย GET
- ไม่มี Admin Route สำหรับ Accept/Reject
- ไม่มี Skill Restore Route
- ไม่มี Notification List Route
- ไม่มี Email Verification Route

การตรวจต้องเทียบ Method + URI + Middleware + Access ไม่ใช้เพียงจำนวน Route
