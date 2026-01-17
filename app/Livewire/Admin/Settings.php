<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    // Tabs
    public $activeTab = 'general';

    // General Settings
    public $appName;
    public $appTagline;
    public $loginTitle;

    // Logo Uploads
    public $logoHeaderUpload;
    public $logoSidebarFullUpload;
    public $logoSidebarSmallUpload;
    public $faviconUpload;

    // Current Logo Values
    public $logoHeader;
    public $logoSidebarFull;
    public $logoSidebarSmall;
    public $favicon;

    // SEO Settings
    public $metaDescription;
    public $metaKeywords;
    public $metaAuthor;
    public $ogImage;
    public $ogImageUpload;
    public $googleAnalyticsId;

    public function mount()
    {
        $settings = Setting::pluck('value', 'key');

        // General
        $this->appName = $settings['app_name'] ?? config('app.name');
        $this->appTagline = $settings['app_tagline'] ?? '';
        $this->loginTitle = $settings['login_title'] ?? 'Selamat Datang';

        // Logos
        $this->logoHeader = $settings['logo_header'] ?? null;
        $this->logoSidebarFull = $settings['logo_sidebar_full'] ?? null;
        $this->logoSidebarSmall = $settings['logo_sidebar_small'] ?? null;
        $this->favicon = $settings['favicon'] ?? null;

        // SEO
        $this->metaDescription = $settings['meta_description'] ?? '';
        $this->metaKeywords = $settings['meta_keywords'] ?? '';
        $this->metaAuthor = $settings['meta_author'] ?? '';
        $this->ogImage = $settings['og_image'] ?? null;
        $this->googleAnalyticsId = $settings['google_analytics_id'] ?? '';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveGeneral()
    {
        $this->validate([
            'appName' => 'required|string|max:60',
            'appTagline' => 'nullable|string|max:150',
            'loginTitle' => 'nullable|string|max:100',
        ]);

        Setting::updateOrCreate(['key' => 'app_name'], ['value' => $this->appName]);
        Setting::updateOrCreate(['key' => 'app_tagline'], ['value' => $this->appTagline]);
        Setting::updateOrCreate(['key' => 'login_title'], ['value' => $this->loginTitle]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Pengaturan umum berhasil disimpan.',
        ]);
    }

    public function saveLogo()
    {
        $this->validate([
            'logoHeaderUpload' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'logoSidebarFullUpload' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'logoSidebarSmallUpload' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'faviconUpload' => 'nullable|image|mimes:png,ico,svg|max:512',
        ]);

        // Process Uploads
        $this->processUpload('logoHeaderUpload', 'logo_header', 'logoHeader');
        $this->processUpload('logoSidebarFullUpload', 'logo_sidebar_full', 'logoSidebarFull');
        $this->processUpload('logoSidebarSmallUpload', 'logo_sidebar_small', 'logoSidebarSmall');
        $this->processUpload('faviconUpload', 'favicon', 'favicon');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Logo dan favicon berhasil disimpan.',
        ]);
    }

    public function saveSeo()
    {
        $this->validate([
            'metaDescription' => 'nullable|string|max:160',
            'metaKeywords' => 'nullable|string|max:255',
            'metaAuthor' => 'nullable|string|max:100',
            'ogImageUpload' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'googleAnalyticsId' => 'nullable|string|max:50',
        ]);

        Setting::updateOrCreate(['key' => 'meta_description'], ['value' => $this->metaDescription]);
        Setting::updateOrCreate(['key' => 'meta_keywords'], ['value' => $this->metaKeywords]);
        Setting::updateOrCreate(['key' => 'meta_author'], ['value' => $this->metaAuthor]);
        Setting::updateOrCreate(['key' => 'google_analytics_id'], ['value' => $this->googleAnalyticsId]);

        // Process OG Image upload
        $this->processUpload('ogImageUpload', 'og_image', 'ogImage');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Pengaturan SEO berhasil disimpan.',
        ]);
    }

    private function processUpload($uploadProperty, $key, $stateProperty)
    {
        if ($this->$uploadProperty) {
            // Delete old file if exists
            $oldFile = Setting::where('key', $key)->value('value');
            if ($oldFile) {
                Storage::disk('public')->delete($oldFile);
            }

            // Store new file
            $path = $this->$uploadProperty->store('settings', 'public');

            // Save to DB
            Setting::updateOrCreate(['key' => $key], ['value' => $path]);

            // Update local state
            $this->$stateProperty = $path;

            // Reset upload input
            $this->reset($uploadProperty);
        }
    }

    public function deleteLogo($key)
    {
        $path = Setting::where('key', $key)->value('value');

        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::where('key', $key)->update(['value' => null]);

            // Update local state
            $stateMap = [
                'logo_header' => 'logoHeader',
                'logo_sidebar_full' => 'logoSidebarFull',
                'logo_sidebar_small' => 'logoSidebarSmall',
                'favicon' => 'favicon',
                'og_image' => 'ogImage',
            ];

            if (isset($stateMap[$key])) {
                $this->{$stateMap[$key]} = null;
            }

            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Terhapus!',
                'text' => 'File berhasil dihapus.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('layouts.dashboard')
            ->title('Pengaturan Aplikasi');
    }
}
