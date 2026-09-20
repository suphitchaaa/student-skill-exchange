<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="เรียนรู้และแบ่งปันทักษะกับเพื่อนในมหาวิทยาลัย ผ่านระบบแลกเปลี่ยนทักษะระหว่างนักศึกษา">
    <title>ระบบแลกเปลี่ยนทักษะ | Student Skill Exchange</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    <header class="landing-header">
        <div class="landing-wrap landing-header-inner">
            <a class="landing-brand" href="{{ route('home') }}" aria-label="ระบบแลกเปลี่ยนทักษะ หน้าแรก">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                <span>ระบบแลกเปลี่ยนทักษะ</span>
            </a>
            <nav class="landing-nav" aria-label="ส่วนต่าง ๆ ของหน้าแรก">
                <a href="#learn">เรียนรู้</a>
                <a href="#share">แบ่งปัน</a>
                <a href="#community">เติบโตไปด้วยกัน</a>
            </nav>
            <span class="landing-header-note">พื้นที่การเรียนรู้ในมหาวิทยาลัย</span>
            <div class="landing-header-actions">
                @guest
                    <a class="landing-header-login" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                    <a class="landing-header-register" href="{{ route('register') }}">สมัครสมาชิก</a>
                @else
                    <a class="landing-header-register" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}">ไปยังภาพรวม</a>
                @endguest
            </div>
        </div>
    </header>

    <main>
        <section class="landing-hero" aria-labelledby="landing-title">
            <div class="landing-wrap landing-hero-grid">
                <div class="landing-hero-copy">
                    <p class="landing-eyebrow">STUDENT SKILL EXCHANGE</p>
                    <h1 id="landing-title">แบ่งปันทักษะ<br><span>เรียนรู้ร่วมกัน</span></h1>
                    <p class="landing-hero-lead">ค้นหาทักษะที่น่าสนใจ แลกเปลี่ยนความรู้ และเรียนรู้ไปพร้อมกับเพื่อนในมหาวิทยาลัย</p>
                    <div class="landing-actions">
                        @guest
                            <a class="landing-button landing-button-primary" href="{{ route('register') }}">เริ่มต้นแบ่งปันทักษะ <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                            <a class="landing-button landing-button-text" href="{{ route('login') }}">เข้าสู่ระบบ <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @else
                            <a class="landing-button landing-button-primary" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}">ไปยังภาพรวม <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        @endguest
                    </div>
                    <p class="landing-hero-footnote">เรียนรู้&nbsp; / &nbsp;แบ่งปัน&nbsp; / &nbsp;เติบโตไปด้วยกัน</p>
                </div>
                <div class="landing-hero-visual" aria-label="พื้นที่แห่งการเรียนรู้และแบ่งปันทักษะ">
                    <div class="landing-visual-index" aria-hidden="true">01 / CAMPUS COMMUNITY</div>
                    <div class="landing-visual-sheet landing-visual-sheet-back" aria-hidden="true"></div>
                    <div class="landing-visual-sheet landing-visual-sheet-front">
                        <div class="landing-visual-topline"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i><span>STUDENT SKILL EXCHANGE</span></div>
                        <p>พื้นที่สำหรับ<br><strong>ความรู้ของทุกคน</strong></p>
                        <div class="landing-visual-divider"></div>
                        <span class="landing-visual-caption">เพื่อนคนหนึ่งอาจมีสิ่งที่คุณอยากเรียนรู้</span>
                    </div>
                    <div class="landing-visual-side" aria-hidden="true">LEARN TOGETHER — SHARE TOGETHER</div>
                </div>
            </div>
        </section>

        <section class="landing-values" id="learn-share-grow" aria-labelledby="landing-values-title">
            <div class="landing-wrap">
                <div class="landing-section-heading">
                    <p class="landing-section-label">พื้นที่ของการแลกเปลี่ยน</p>
                    <h2 id="landing-values-title">เริ่มจากสิ่งที่อยากรู้<br>ต่อยอดด้วย<span>สิ่งที่เราถนัด</span></h2>
                </div>
                <div class="landing-value-list">
                    <article class="landing-value landing-value-learn" id="learn">
                        <span class="landing-value-number">01</span>
                        <i class="bi bi-book" aria-hidden="true"></i>
                        <div><h3>เรียนรู้</h3><p>ค้นหาทักษะใหม่จากเพื่อนในมหาวิทยาลัย</p></div>
                    </article>
                    <article class="landing-value landing-value-share" id="share">
                        <span class="landing-value-number">02</span>
                        <i class="bi bi-chat-square-heart" aria-hidden="true"></i>
                        <div><h3>แบ่งปัน</h3><p>แบ่งปันสิ่งที่คุณถนัดให้กับผู้อื่น</p></div>
                    </article>
                    <article class="landing-value landing-value-grow">
                        <span class="landing-value-number">03</span>
                        <i class="bi bi-people" aria-hidden="true"></i>
                        <div><h3>เติบโต</h3><p>สร้างโอกาสและเรียนรู้ไปด้วยกัน</p></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="landing-process" id="how-it-works" aria-labelledby="landing-process-title">
            <div class="landing-wrap landing-process-grid">
                <div class="landing-process-intro">
                    <p class="landing-section-label">HOW IT WORKS</p>
                    <h2 id="landing-process-title">เริ่มแลกเปลี่ยน<br>ได้ในสามขั้นตอน</h2>
                    <p>เพิ่มทักษะของคุณ ค้นหาเพื่อนที่สนใจ แล้วส่งคำขอเพื่อเริ่มเรียนรู้ร่วมกัน</p>
                </div>
                <ol class="landing-steps">
                    <li><span>01</span><p>เพิ่มทักษะของคุณ</p><i class="bi bi-arrow-up-right" aria-hidden="true"></i></li>
                    <li><span>02</span><p>ค้นหานักศึกษาและทักษะที่สนใจ</p><i class="bi bi-arrow-up-right" aria-hidden="true"></i></li>
                    <li><span>03</span><p>ส่งคำขอแลกเปลี่ยนทักษะ</p><i class="bi bi-arrow-up-right" aria-hidden="true"></i></li>
                </ol>
            </div>
        </section>

        <section class="landing-community" id="community" aria-labelledby="landing-community-title">
            <div class="landing-wrap landing-community-grid">
                <p class="landing-section-label">OUR CAMPUS COMMUNITY</p>
                <div>
                    <h2 id="landing-community-title">ทุกคนมีสิ่งที่แบ่งปัน<br>และมีสิ่งใหม่ให้<span>เรียนรู้</span></h2>
                    <p>พบเพื่อนที่สนใจในสิ่งเดียวกัน แลกเปลี่ยนทักษะที่แต่ละคนถนัด และเรียนรู้ร่วมกันในชุมชนมหาวิทยาลัย</p>
                </div>
                <div class="landing-community-mark" aria-hidden="true"><i class="bi bi-people-fill"></i><span>TOGETHER</span></div>
            </div>
        </section>

        <section class="landing-final" aria-labelledby="landing-final-title">
            <div class="landing-wrap landing-final-grid">
                <div>
                    <p class="landing-section-label">เริ่มต้นที่นี่</p>
                    <h2 id="landing-final-title">เริ่มต้นแบ่งปัน<br><span>ทักษะของคุณ</span></h2>
                </div>
                <div class="landing-final-content">
                    <p>ค้นหาสิ่งที่อยากเรียนรู้ และแบ่งปันสิ่งที่คุณถนัดไปพร้อมกับเพื่อนในมหาวิทยาลัย</p>
                    <div class="landing-actions">
                        @guest
                            <a class="landing-button landing-button-primary" href="{{ route('register') }}">สมัครสมาชิก <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                            <a class="landing-button landing-button-text" href="{{ route('login') }}">เข้าสู่ระบบ <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        @else
                            <a class="landing-button landing-button-primary" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}">ไปยังภาพรวม <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        @endguest
                    </div>
                </div>
                <div class="landing-final-mark" aria-hidden="true">
                    <i class="bi bi-people-fill"></i>
                    <span>LEARN / SHARE / GROW</span>
                </div>
            </div>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="landing-wrap landing-footer-inner">
            <div class="landing-footer-brand"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i><span>ระบบแลกเปลี่ยนทักษะ</span></div>
            <p>พื้นที่การเรียนรู้และแบ่งปันทักษะในมหาวิทยาลัย</p>
            <span>STUDENT SKILL EXCHANGE</span>
        </div>
    </footer>
</body>
</html>
