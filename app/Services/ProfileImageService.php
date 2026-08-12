<?php

namespace App\Services;

use App\Models\StudentProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ProfileImageService
{
    private const DISK = 'public';

    /** DB transaction ไม่ครอบคลุมไฟล์ จึงต้องชดเชยทุกกรณีที่ DB กับ storage ไม่สอดคล้องกัน. */
    public function store(StudentProfile $profile, UploadedFile $image): void
    {
        $path = $this->storeNewFile($profile, $image);
        $oldPath = $profile->profile_image;

        try {
            DB::transaction(function () use ($profile, $path): void {
                $this->persistImagePath($profile, $path);
            });
        } catch (Throwable $exception) {
            $this->deleteNewFile($path);

            throw $exception;
        }

        if ($oldPath !== null) {
            $this->deleteOldFile($profile, $oldPath);
        }
    }

    public function remove(StudentProfile $profile): void
    {
        $oldPath = $profile->profile_image;

        if ($oldPath === null) {
            return;
        }

        DB::transaction(function () use ($profile): void {
            $profile->update(['profile_image' => null]);
        });

        $this->deleteOldFile($profile, $oldPath);
    }

    protected function persistImagePath(StudentProfile $profile, string $path): void
    {
        $profile->update(['profile_image' => $path]);
    }

    private function storeNewFile(StudentProfile $profile, UploadedFile $image): string
    {
        // ใช้ UUID แทนชื่อจากผู้ใช้เพื่อป้องกันชื่อไฟล์ที่ไม่ปลอดภัย.
        $directory = $this->directoryFor($profile);
        $path = $directory.Str::uuid().'.'.$image->extension();

        if (! Storage::disk(self::DISK)->put($path, $image->getContent())) {
            throw new RuntimeException('ไม่สามารถบันทึกรูปโปรไฟล์ได้');
        }

        return $path;
    }

    private function deleteNewFile(string $path): void
    {
        if (! Storage::disk(self::DISK)->delete($path)) {
            Log::warning('ไม่สามารถลบไฟล์รูปโปรไฟล์ใหม่เพื่อชดเชยได้', ['path' => $path]);
        }
    }

    private function deleteOldFile(StudentProfile $profile, string $path): void
    {
        if (! $this->belongsToProfile($profile, $path)) {
            Log::warning('ปฏิเสธการลบไฟล์รูปโปรไฟล์นอกไดเรกทอรีของผู้ใช้', ['path' => $path]);

            return;
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            Log::info('ไม่พบไฟล์รูปโปรไฟล์เดิมขณะลบ', ['path' => $path]);

            return;
        }

        if (! $disk->delete($path)) {
            Log::warning('ไม่สามารถลบไฟล์รูปโปรไฟล์เก่าได้', ['path' => $path]);
        }
    }

    private function directoryFor(StudentProfile $profile): string
    {
        return 'profile-images/'.$profile->user_id.'/';
    }

    private function belongsToProfile(StudentProfile $profile, string $path): bool
    {
        return Str::startsWith($path, $this->directoryFor($profile));
    }
}
