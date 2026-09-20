@extends('layouts.app')

@section('title', 'ทักษะของฉัน')
@section('page-title', 'ทักษะของฉัน')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="content-heading d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h1 class="mb-1">จัดการทักษะของฉัน</h1>
            <p class="text-secondary mb-0">ระบุทักษะที่คุณสอนได้และทักษะที่ต้องการเรียนรู้</p>
        </div>
    </div>

    <ul class="nav nav-tabs workflow-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeSkillType === 'offered' ? 'active' : '' }}" href="{{ route('user-skills.index', ['type' => 'offered']) }}">
                ทักษะที่สอนได้
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSkillType === 'wanted' ? 'active' : '' }}" href="{{ route('user-skills.index', ['type' => 'wanted']) }}">
                ทักษะที่ต้องการเรียน
            </a>
        </li>
    </ul>

    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <section class="campus-card bg-white skill-panel">
                <h2 class="h5 mb-3">เพิ่ม{{ $activeSkillType === 'offered' ? 'ทักษะที่สอนได้' : 'ทักษะที่ต้องการเรียน' }}</h2>
                <form action="{{ route('user-skills.store') }}" method="POST">
                    @csrf
                    <input name="skill_type" type="hidden" value="{{ $activeSkillType }}">
                    <div class="mb-3">
                        <div class="skill-picker" data-skill-picker>
                            <label class="form-label" for="skill_search">ทักษะ</label>
                            <input class="form-control @if($errors->has('skill_id') || $errors->has('skill_name')) is-invalid @endif" id="skill_search" type="search" value="{{ old('skill_name', '') }}" placeholder="ค้นหาหรือเพิ่มทักษะ..." autocomplete="off" role="combobox" aria-autocomplete="list" aria-haspopup="listbox" aria-controls="skill_suggestions" aria-expanded="false" aria-describedby="skill_picker_help @if($errors->has('skill_id')) skill_id_error @endif @if($errors->has('skill_name')) skill_name_error @endif" @if($errors->has('skill_id') || $errors->has('skill_name')) aria-invalid="true" @endif>
                            <input id="skill_id" name="skill_id" type="hidden" value="{{ old('skill_id') }}" disabled>
                            <input id="skill_name" name="skill_name" type="hidden" value="{{ old('skill_name') }}" disabled>
                            <div class="skill-picker-list" id="skill_suggestions" role="listbox" hidden>
                                @foreach ($availableSkills as $skill)
                                    <button class="skill-suggestion" id="skill_suggestion_{{ $skill->id }}" type="button" role="option" tabindex="-1" data-skill-option data-skill-id="{{ $skill->id }}" data-skill-name="{{ $skill->name }}" data-skill-category="{{ $skill->category }}" data-skill-normalized="{{ $skill->normalized_name }}">
                                        <span class="skill-suggestion-name">{{ $skill->name }}</span>
                                        @if ($skill->category)<span class="skill-suggestion-category">{{ $skill->category }}</span>@endif
                                    </button>
                                @endforeach
                                <button class="skill-suggestion skill-suggestion-create" id="skill_suggestion_create" type="button" role="option" tabindex="-1" hidden>+ เพิ่ม “<span data-new-skill-label></span>” เป็นทักษะของฉัน</button>
                                <div class="skill-picker-empty" data-skill-empty hidden>ไม่พบทักษะที่ตรงกับคำค้น</div>
                            </div>
                        </div>
                        <div class="form-text" id="skill_picker_help">เลือกทักษะที่มีอยู่ หรือเพิ่มชื่อใหม่จากรายการที่แสดง</div>
                        <div class="skill-picker-choice" id="skill_picker_choice" aria-live="polite"></div>
                        @error('skill_id') <div class="invalid-feedback d-block" id="skill_id_error" role="alert">{{ $message }}</div> @enderror
                        @error('skill_name') <div class="invalid-feedback d-block" id="skill_name_error" role="alert">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>เพิ่มทักษะ</button>
                </form>
            </section>
        </div>
        <div class="col-12 col-xl-7">
            <section class="campus-card bg-white skill-panel">
                <h2 class="h5 mb-3">รายการทักษะ</h2>
                @forelse ($userSkills as $userSkill)
                    <article class="skill-entry">
                        <form action="{{ route('user-skills.update', $userSkill) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="skill_id_{{ $userSkill->id }}">ทักษะ</label>
                                    <select class="form-select" id="skill_id_{{ $userSkill->id }}" name="skill_id" required>
                                        @foreach ($availableSkills as $skill)
                                            <option value="{{ $skill->id }}" @selected($userSkill->skill_id === $skill->id)>{{ $skill->name }}{{ $skill->category ? ' ('.$skill->category.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="skill_type_{{ $userSkill->id }}">ประเภท</label>
                                    <select class="form-select" id="skill_type_{{ $userSkill->id }}" name="skill_type" required>
                                        <option value="offered" @selected($userSkill->skill_type === 'offered')>ทักษะที่สอนได้</option>
                                        <option value="wanted" @selected($userSkill->skill_type === 'wanted')>ทักษะที่ต้องการเรียน</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description_{{ $userSkill->id }}">รายละเอียดเพิ่มเติม</label>
                                    <textarea class="form-control" id="description_{{ $userSkill->id }}" name="description" rows="2">{{ $userSkill->description }}</textarea>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2">
                                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-pencil-square me-1" aria-hidden="true"></i>บันทึกการแก้ไข</button>
                                </div>
                            </div>
                        </form>
                        <form action="{{ route('user-skills.destroy', $userSkill) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash me-1" aria-hidden="true"></i>ลบทักษะ</button>
                        </form>
                    </article>
                @empty
                    <p class="text-secondary mb-0">ยังไม่มีรายการทักษะในหมวดนี้</p>
                @endforelse
            </section>
        </div>
    </div>

    <script>
        (() => {
            const picker = document.querySelector('[data-skill-picker]');
            const form = picker.closest('form');
            const search = document.getElementById('skill_search');
            const skillId = document.getElementById('skill_id');
            const skillName = document.getElementById('skill_name');
            const list = document.getElementById('skill_suggestions');
            const options = [...list.querySelectorAll('[data-skill-option]')];
            const createOption = document.getElementById('skill_suggestion_create');
            const createLabel = createOption.querySelector('[data-new-skill-label]');
            const empty = list.querySelector('[data-skill-empty]');
            const choice = document.getElementById('skill_picker_choice');
            let activeIndex = -1;

            const clean = (value) => value.replace(/[\s\p{Z}]+/gu, ' ').trim();
            // เติม Unicode full case folds ที่ JavaScript lowercase ไม่แปลงให้ตรงกับ MB_CASE_FOLD
            const caseFoldOverrides = {
                'µ': 'μ', 'ß': 'ss', 'ŉ': 'ʼn', 'ſ': 's', 'ǰ': 'ǰ', 'ͅ': 'ι', 'ΐ': 'ΐ', 'ΰ': 'ΰ', 'ς': 'σ',
                'ϐ': 'β', 'ϑ': 'θ', 'ϕ': 'φ', 'ϖ': 'π', 'ϰ': 'κ', 'ϱ': 'ρ', 'ϵ': 'ε', 'և': 'եւ',
                'ᲀ': 'в', 'ᲁ': 'д', 'ᲂ': 'о', 'ᲃ': 'с', 'ᲄ': 'т', 'ᲅ': 'т', 'ᲆ': 'ъ', 'ᲇ': 'ѣ', 'ᲈ': 'ꙋ',
                'ẖ': 'ẖ', 'ẗ': 'ẗ', 'ẘ': 'ẘ', 'ẙ': 'ẙ', 'ẚ': 'aʾ', 'ẛ': 'ṡ',
                'ὐ': 'ὐ', 'ὒ': 'ὒ', 'ὔ': 'ὔ', 'ὖ': 'ὖ',
                'ᾀ': 'ἀι', 'ᾁ': 'ἁι', 'ᾂ': 'ἂι', 'ᾃ': 'ἃι', 'ᾄ': 'ἄι', 'ᾅ': 'ἅι', 'ᾆ': 'ἆι', 'ᾇ': 'ἇι',
                'ᾐ': 'ἠι', 'ᾑ': 'ἡι', 'ᾒ': 'ἢι', 'ᾓ': 'ἣι', 'ᾔ': 'ἤι', 'ᾕ': 'ἥι', 'ᾖ': 'ἦι', 'ᾗ': 'ἧι',
                'ᾠ': 'ὠι', 'ᾡ': 'ὡι', 'ᾢ': 'ὢι', 'ᾣ': 'ὣι', 'ᾤ': 'ὤι', 'ᾥ': 'ὥι', 'ᾦ': 'ὦι', 'ᾧ': 'ὧι',
                'ᾲ': 'ὰι', 'ᾳ': 'αι', 'ᾴ': 'άι', 'ᾶ': 'ᾶ', 'ᾷ': 'ᾶι', 'ι': 'ι',
                'ῂ': 'ὴι', 'ῃ': 'ηι', 'ῄ': 'ήι', 'ῆ': 'ῆ', 'ῇ': 'ῆι', 'ῒ': 'ῒ', 'ΐ': 'ΐ', 'ῖ': 'ῖ', 'ῗ': 'ῗ',
                'ῢ': 'ῢ', 'ΰ': 'ΰ', 'ῤ': 'ῤ', 'ῦ': 'ῦ', 'ῧ': 'ῧ',
                'ῲ': 'ὼι', 'ῳ': 'ωι', 'ῴ': 'ώι', 'ῶ': 'ῶ', 'ῷ': 'ῶι',
                'ﬀ': 'ff', 'ﬁ': 'fi', 'ﬂ': 'fl', 'ﬃ': 'ffi', 'ﬄ': 'ffl', 'ﬅ': 'st', 'ﬆ': 'st',
                'ﬓ': 'մն', 'ﬔ': 'մե', 'ﬕ': 'մի', 'ﬖ': 'վն', 'ﬗ': 'մխ',
            };
            const fold = (value) => [...clean(value).toLowerCase()].map((character) => {
                const code = character.codePointAt(0);
                if (code >= 0xAB70 && code <= 0xABBF) return String.fromCodePoint(code - 0x97D0);
                if (code >= 0x13F8 && code <= 0x13FD) return String.fromCodePoint(code - 8);
                return caseFoldOverrides[character] ?? character;
            }).join('');
            const visibleOptions = () => [...options.filter((option) => !option.hidden), ...(!createOption.hidden ? [createOption] : [])];
            const close = () => {
                list.hidden = true;
                search.setAttribute('aria-expanded', 'false');
                search.removeAttribute('aria-activedescendant');
                activeIndex = -1;
                list.querySelectorAll('.is-active').forEach((option) => option.classList.remove('is-active'));
            };
            const open = () => {
                list.hidden = false;
                search.setAttribute('aria-expanded', 'true');
            };
            const clearSelection = () => {
                skillId.value = '';
                skillId.disabled = true;
                skillName.value = '';
                skillName.disabled = true;
                choice.textContent = '';
            };
            const chooseExisting = (option) => {
                clearSelection();
                skillId.value = option.dataset.skillId;
                skillId.disabled = false;
                search.value = option.dataset.skillName;
                choice.textContent = `เลือกทักษะที่มีอยู่: ${option.dataset.skillName}`;
                search.setCustomValidity('');
                search.removeAttribute('aria-invalid');
                close();
            };
            const chooseNew = () => {
                const name = clean(search.value);
                clearSelection();
                skillName.value = name;
                skillName.disabled = false;
                search.value = name;
                choice.textContent = `เพิ่มทักษะใหม่: ${name}`;
                search.setCustomValidity('');
                search.removeAttribute('aria-invalid');
                close();
            };
            const render = () => {
                const query = fold(search.value);
                const name = clean(search.value);
                options.forEach((option) => {
                    option.hidden = query !== '' && !fold(option.dataset.skillName).includes(query) && !fold(option.dataset.skillCategory).includes(query);
                });
                const exactMatch = options.some((option) => option.dataset.skillNormalized === query);
                createOption.hidden = name === '' || exactMatch || name.length > 255 || /[<>\p{Cc}\p{Cf}]/u.test(name);
                createLabel.textContent = name;
                empty.hidden = visibleOptions().length > 0;
                activeIndex = -1;
                search.removeAttribute('aria-activedescendant');
                list.querySelectorAll('.is-active').forEach((option) => option.classList.remove('is-active'));
            };
            const move = (step) => {
                const visible = visibleOptions();
                if (visible.length === 0) return;
                visible.forEach((option) => option.classList.remove('is-active'));
                activeIndex = activeIndex < 0
                    ? (step > 0 ? 0 : visible.length - 1)
                    : (activeIndex + step + visible.length) % visible.length;
                visible[activeIndex].classList.add('is-active');
                search.setAttribute('aria-activedescendant', visible[activeIndex].id);
                visible[activeIndex].scrollIntoView({ block: 'nearest' });
            };
            const choose = (option) => option === createOption ? chooseNew() : chooseExisting(option);

            search.addEventListener('focus', () => { render(); open(); });
            search.addEventListener('input', () => { clearSelection(); search.setCustomValidity(''); search.removeAttribute('aria-invalid'); render(); open(); });
            search.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') { event.preventDefault(); close(); return; }
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (list.hidden) { render(); open(); }
                    move(event.key === 'ArrowDown' ? 1 : -1);
                } else if (event.key === 'Enter' && !list.hidden) {
                    event.preventDefault();
                    const visible = visibleOptions();
                    if (visible.length > 0) choose(visible[activeIndex < 0 ? 0 : activeIndex]);
                }
            });
            list.addEventListener('click', (event) => {
                const option = event.target.closest('.skill-suggestion');
                if (option && !option.hidden) choose(option);
            });
            list.addEventListener('mousedown', (event) => event.preventDefault());
            picker.addEventListener('focusout', (event) => { if (!picker.contains(event.relatedTarget)) close(); });
            document.addEventListener('pointerdown', (event) => { if (!picker.contains(event.target)) close(); });
            form.addEventListener('submit', (event) => {
                if (skillId.disabled && skillName.disabled) {
                    event.preventDefault();
                    search.setCustomValidity('กรุณาเลือกทักษะเดิมหรือเลือกเพิ่มทักษะใหม่จากรายการ');
                    search.reportValidity();
                }
            });

            const previousId = skillId.value;
            const previousName = skillName.value;
            clearSelection();
            if (previousId && !previousName) {
                const previousOption = options.find((option) => option.dataset.skillId === previousId);
                if (previousOption) chooseExisting(previousOption);
            } else if (previousName && !previousId) {
                search.value = previousName;
                render();
                const existingOption = options.find((option) => option.dataset.skillNormalized === fold(previousName));
                if (existingOption) chooseExisting(existingOption);
                else if (!createOption.hidden) chooseNew();
            }
        })();
    </script>
@endsection
