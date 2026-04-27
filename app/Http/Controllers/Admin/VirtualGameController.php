<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VirtualGame;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VirtualGameController extends Controller
{
    public function index()
    {
        $games = VirtualGame::orderBy('sort_order')->get();
        return view('admin.virtual-games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.virtual-games.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'tagline' => 'nullable|string|max:150',
            'provider' => 'nullable|string|max:80',
            'volatility' => 'nullable|string|max:50',
            'rtp' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'script' => 'nullable|file|mimes:js,text/javascript,application/javascript|max:10240',
            'package' => 'nullable|file|mimes:zip|max:20480',
            'assets.*' => 'nullable|file|mimes:png,jpg,jpeg,gif,svg,webp,mp3,ogg,wav,json,txt|max:10240',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $folder = $this->getVirtualGameFolder($data['slug']);

        if ($request->hasFile('package')) {
            Storage::disk('public')->deleteDirectory($folder);
            $this->extractPackageToFolder($request->file('package'), $folder);
            if (!$request->hasFile('script')) {
                $data['script_path'] = $this->detectEntryScript($folder);
            }
        }

        if ($request->hasFile('script')) {
            $data['script_path'] = $this->storeVirtualGameFile($request->file('script'), $folder);
        }

        if ($request->hasFile('assets')) {
            foreach ($request->file('assets') as $asset) {
                $this->storeVirtualGameFile($asset, $folder);
            }
        }

        VirtualGame::create($data);

        return redirect()->route('admin.virtual-games.index')->with('success', 'Virtual game created.');
    }

    public function edit(VirtualGame $virtualGame)
    {
        return view('admin.virtual-games.edit', compact('virtualGame'));
    }

    public function update(Request $request, VirtualGame $virtualGame)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'tagline' => 'nullable|string|max:150',
            'provider' => 'nullable|string|max:80',
            'volatility' => 'nullable|string|max:50',
            'rtp' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'script' => 'nullable|file|mimes:js,text/javascript,application/javascript|max:10240',
            'package' => 'nullable|file|mimes:zip|max:20480',
            'assets.*' => 'nullable|file|mimes:png,jpg,jpeg,gif,svg,webp,mp3,ogg,wav,json,txt|max:10240',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $newSlug = Str::slug($request->input('name'));
        $data['slug'] = $newSlug;
        $data['is_active'] = $request->boolean('is_active');

        if ($newSlug !== $virtualGame->slug) {
            $oldFolder = $this->getVirtualGameFolder($virtualGame->slug);
            $newFolder = $this->getVirtualGameFolder($newSlug);
            if (Storage::disk('public')->exists($oldFolder)) {
                Storage::disk('public')->moveDirectory($oldFolder, $newFolder);
            }
            if ($virtualGame->script_path) {
                $data['script_path'] = preg_replace(
                    "#^virtual-games/{$virtualGame->slug}#",
                    "virtual-games/{$newSlug}",
                    $virtualGame->script_path
                );
            }
        }

        $folder = $this->getVirtualGameFolder($newSlug);

        if ($request->hasFile('package')) {
            Storage::disk('public')->deleteDirectory($folder);
            $this->extractPackageToFolder($request->file('package'), $folder);
            if (!$request->hasFile('script')) {
                $data['script_path'] = $this->detectEntryScript($folder);
            }
        }

        if ($request->hasFile('script')) {
            if ($virtualGame->script_path && Storage::disk('public')->exists($virtualGame->script_path)) {
                Storage::disk('public')->delete($virtualGame->script_path);
            }
            $data['script_path'] = $this->storeVirtualGameFile($request->file('script'), $folder);
        }

        if ($request->hasFile('assets')) {
            foreach ($request->file('assets') as $asset) {
                $this->storeVirtualGameFile($asset, $folder);
            }
        }

        $virtualGame->update($data);

        return redirect()->route('admin.virtual-games.index')->with('success', 'Virtual game updated.');
    }

    public function destroy(VirtualGame $virtualGame)
    {
        $folder = $this->getVirtualGameFolder($virtualGame->slug);
        Storage::disk('public')->deleteDirectory($folder);

        $virtualGame->delete();

        return redirect()->route('admin.virtual-games.index')->with('success', 'Virtual game deleted.');
    }

    private function getVirtualGameFolder(string $slug): string
    {
        return "virtual-games/{$slug}";
    }

    private function storeVirtualGameFile(UploadedFile $file, string $folder): string
    {
        $fileName = $file->getClientOriginalName();
        return Storage::disk('public')->putFileAs($folder, $file, $fileName);
    }

    private function extractPackageToFolder(UploadedFile $package, string $folder): void
    {
        $zipPath = $package->getRealPath();
        $zip = new \ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Unable to open uploaded ZIP package.');
        }

        $storagePath = Storage::disk('public')->path($folder);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $zip->extractTo($storagePath);
        $zip->close();
    }

    private function detectEntryScript(string $folder): ?string
    {
        $preferred = ['index.js', 'main.js', 'game.js'];
        $basePath = Storage::disk('public')->path($folder);

        foreach ($preferred as $fileName) {
            if (file_exists($basePath . DIRECTORY_SEPARATOR . $fileName)) {
                return "$folder/{$fileName}";
            }
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath));
        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'js') {
                $relative = ltrim(str_replace($basePath, '', $file->getPathname()), DIRECTORY_SEPARATOR);
                return "$folder/{$relative}";
            }
        }

        return null;
    }
}
