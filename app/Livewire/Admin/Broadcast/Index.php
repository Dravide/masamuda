<?php

namespace App\Livewire\Admin\Broadcast;

use Livewire\Component;
use App\Models\School;
use App\Models\Project;
use App\Models\Student;
use App\Services\OneApiService;
use Illuminate\Support\Str;

class Index extends Component
{
    public $target = 'all'; // all, school, project, student
    public $school_id;
    public $project_id;
    public $student_id;

    public $type = 'message'; // message, photo_link
    public $message = '';

    public $schools = [];
    public $projects = [];
    public $students = []; // For specific student selection (searchable ideal, but simple select for now)

    public function mount()
    {
        $this->schools = School::all();
        $this->projects = Project::all();
    }

    public function updatedTarget()
    {
        $this->reset(['school_id', 'project_id', 'student_id']);
    }

    public function getRecipients()
    {
        // Ideally utilize query builder for efficiency
        $query = Student::query();

        if ($this->target === 'school') {
            $query->where('school_id', $this->school_id);
        } elseif ($this->target === 'project') {
            $query->where('project_id', $this->project_id);
        } elseif ($this->target === 'student') {
            $query->where('id', $this->student_id);
        }

        return $query->get();
    }

    public function send(OneApiService $api)
    {
        $this->validate([
            'target' => 'required',
            'school_id' => 'required_if:target,school',
            'project_id' => 'required_if:target,project',
            'student_id' => 'required_if:target,student',
            'message' => 'required_unless:type,photo_link', // Optional if photo_link automatic
        ]);

        $recipients = $this->getRecipients();
        $count = 0;
        $fails = 0;

        foreach ($recipients as $student) {
            $content = $this->message;

            if ($this->type === 'photo_link') {
                // Generate magic link
                // Assuming route for magic access exists: student.public.profile {token}
                $link = route('student.public.profile', ['token' => $student->magic_token]);

                // Construct personalized message
                $content = "Halo {$student->name}, lihat foto kegiatan project kamu disini: {$link}";
            }

            // Using whatsapp number. Ensure it handles formatting (e.g. 08 -> 628)
            $phone = $student->whatsapp;

            // Basic formatting fix commonly needed for indo numbers
            if (Str::startsWith($phone, '08')) {
                $phone = '62' . substr($phone, 1);
            }

            if ($phone) {
                $result = $api->sendMessage($phone, $content);
                if ($result['success']) {
                    $count++;
                } else {
                    $fails++;
                }
            }
        }

        session()->flash('success', "Broadcast selesai. Berhasil: {$count}, Gagal/No Number: {$fails}");
        $this->reset(['message', 'type']);
    }

    public function render()
    {
        // Load students if target is 'student', maybe limit or use search later
        if ($this->target === 'student') {
            $this->students = Student::all();
        }

        return view('livewire.admin.broadcast.index')
            ->layout('layouts.dashboard')->title('Broadcast Admin');
    }
}
