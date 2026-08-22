<form method="POST" action="{{ $action }}" class="campus-card bg-white p-4">
    @csrf @if($method !== 'POST') @method($method) @endif
    <div class="mb-3"><label class="form-label" for="skill-name">ชื่อทักษะ</label><input id="skill-name" name="name" value="{{ old('name', $skill->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label" for="skill-category">หมวดหมู่</label><input id="skill-category" name="category" value="{{ old('category', $skill->category) }}" class="form-control @error('category') is-invalid @enderror" required>@error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="form-check mb-4"><input type="hidden" name="is_active" value="0"><input id="skill-active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $skill->is_active))><label class="form-check-label" for="skill-active">เปิดให้ใช้งานในฝั่งนักศึกษา</label></div>
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</form>
