<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\StudentProfile;
use App\Services\ProfileImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $profile = $this->profileFor($request);
        Gate::authorize('view', $profile);

        return view('profile.show', $this->viewData($profile));
    }

    public function edit(Request $request): View
    {
        $profile = $this->profileFor($request);
        Gate::authorize('update', $profile);

        return view('profile.edit', $this->viewData($profile));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $profile = $this->profileFor($request);
        Gate::authorize('update', $profile);
        $profile->update($request->validated());

        return to_route('profile.show')->with('success', 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }

    public function storeImage(ProfileImageRequest $request, ProfileImageService $profileImageService): RedirectResponse
    {
        $profile = $this->profileFor($request);
        Gate::authorize('update', $profile);

        try {
            $profileImageService->store($profile, $request->file('profile_image'));
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors(['profile_image' => 'ไม่สามารถบันทึกรูปโปรไฟล์ได้ โปรดลองอีกครั้ง']);
        }

        return to_route('profile.edit')->with('success', 'บันทึกรูปโปรไฟล์เรียบร้อยแล้ว');
    }

    public function destroyImage(Request $request, ProfileImageService $profileImageService): RedirectResponse
    {
        $profile = $this->profileFor($request);
        Gate::authorize('delete', $profile);

        try {
            $profileImageService->remove($profile);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['profile_image' => 'ไม่สามารถลบรูปโปรไฟล์ได้ โปรดลองอีกครั้ง']);
        }

        return to_route('profile.edit')->with('success', 'ลบรูปโปรไฟล์เรียบร้อยแล้ว');
    }

    private function profileFor(Request $request): StudentProfile
    {
        return $request->user()->studentProfile()->firstOrCreate();
    }

    private function viewData(StudentProfile $profile): array
    {
        return [
            'profile' => $profile,
            // ใช้ URL จาก request เพื่อรองรับการติดตั้งใต้ subdirectory ของ Apache
            'profileImageUrl' => $profile->profile_image === null
                ? null
                : asset('storage/'.ltrim($profile->profile_image, '/')),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ];
    }
}
