<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ZipArchive;

class PhotoDownloadController extends Controller
{
    /**
     * Download all photos for a project (Sekolah role).
     */
    public function downloadAllSekolah(Request $request, Project $project)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        // Verify project belongs to this school
        if ($project->school_id !== $school->id) {
            abort(403);
        }

        return $this->buildAndStreamZip($project, $school->id);
    }

    /**
     * Download a single student's photos (Sekolah role).
     */
    public function downloadStudentSekolah(Request $request, Project $project, Student $student)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        // Verify project and student belong to this school
        if ($project->school_id !== $school->id || $student->school_id !== $school->id || $student->project_id !== $project->id) {
            abort(403);
        }

        return $this->downloadStudentPhotos($student, $project);
    }

    /**
     * Download all photos for a project (Guru role).
     */
    public function downloadAllGuru(Request $request, Project $project)
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher || $project->school_id !== $teacher->school_id) {
            abort(403);
        }

        return $this->buildAndStreamZip($project, $teacher->school_id);
    }

    /**
     * Download a single student's photos (Guru role).
     */
    public function downloadStudentGuru(Request $request, Project $project, Student $student)
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher || $project->school_id !== $teacher->school_id || $student->school_id !== $teacher->school_id || $student->project_id !== $project->id) {
            abort(403);
        }

        return $this->downloadStudentPhotos($student, $project);
    }

    /**
     * Build ZIP and stream it to the browser.
     */
    private function buildAndStreamZip(Project $project, int $schoolId)
    {
        // Increase limits for large zip operations
        set_time_limit(600);
        ini_set('memory_limit', '256M');

        $hasPhotos = Student::where('school_id', $schoolId)
            ->where('project_id', $project->id)
            ->whereHas('photos', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->exists();

        if (!$hasPhotos) {
            return back()->with('error', 'Tidak ada foto untuk diunduh.');
        }

        $zipFileName = 'foto_' . Str::slug($project->name) . '_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'Gagal membuat file zip.');
        }

        // Use cursor to iterate without loading all students into memory
        $students = Student::where('school_id', $schoolId)
            ->where('project_id', $project->id)
            ->whereHas('photos', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->cursor();

        foreach ($students as $student) {
            $folderName = $student->nis . '_' . Str::slug($student->name);
            $photos = $student->photos()->where('project_id', $project->id)->get();
            foreach ($photos as $photo) {
                $filePath = storage_path('app/public/' . $photo->file_path);
                if (file_exists($filePath)) {
                    $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
                    $zip->addFile($filePath, $folderName . '/' . $photo->photo_type . '_' . $photo->id . '.' . $extension);
                }
            }
        }

        $zip->close();

        // Stream the file in chunks — never load whole ZIP into PHP memory
        $fileSize = filesize($zipPath);

        return response()->stream(function () use ($zipPath) {
            // Disable output buffering to prevent PHP from buffering the whole file
            if (ob_get_level()) {
                ob_end_clean();
            }

            $stream = fopen($zipPath, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 2 * 1024 * 1024); // 2MB chunks
                flush();
            }
            fclose($stream);
            @unlink($zipPath); // Clean up temp file
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Download photos for a single student.
     */
    private function downloadStudentPhotos(Student $student, Project $project)
    {
        set_time_limit(300);

        $photos = $student->photos()->where('project_id', $project->id)->get();

        if ($photos->isEmpty()) {
            return back()->with('error', 'Siswa ini belum memiliki foto.');
        }

        if ($photos->count() === 1) {
            $photo = $photos->first();
            $filePath = storage_path('app/public/' . $photo->file_path);
            if (!file_exists($filePath)) {
                return back()->with('error', 'File foto tidak ditemukan.');
            }
            return response()->download($filePath);
        }

        // Multiple files - create zip
        $zipFileName = 'foto_' . Str::slug($student->name) . '_' . $student->nis . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($photos as $photo) {
                $filePath = storage_path('app/public/' . $photo->file_path);
                if (file_exists($filePath)) {
                    $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
                    $zip->addFile($filePath, $photo->photo_type . '_' . $photo->id . '.' . $extension);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Gagal membuat file zip.');
    }
}
