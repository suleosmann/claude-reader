<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Project;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.reader')]
#[Title('Claude Reader')]
class Reader extends Component
{
    public ?int $activeChatId = null;

    public bool $fixEncoding = true;

    public function mount(): void
    {
        $user = Auth::user();

        // Give brand-new users a starter project + chat.
        if ($user->projects()->count() === 0) {
            $project = $user->projects()->create(['name' => 'My Project', 'position' => 0]);
            $chat = $project->chats()->create(['user_id' => $user->id, 'position' => 0, 'content' => '']);
            $this->activeChatId = $chat->id;
            return;
        }

        $this->activeChatId = $user->chats()->orderBy('id')->value('id');
    }

    /* ---------------- Selection ---------------- */

    public function selectChat(int $id): void
    {
        if ($this->ownsChat($id)) {
            $this->activeChatId = $id;
        }
    }

    public function toggleCollapse(int $projectId): void
    {
        $project = $this->userProject($projectId);
        $project->update(['collapsed' => ! $project->collapsed]);
    }

    /* ---------------- Projects ---------------- */

    public function addProject(string $name = ''): void
    {
        $name = trim($name);
        $user = Auth::user();
        $project = $user->projects()->create([
            'name' => $name !== '' ? $name : 'New Project',
            'position' => (int) $user->projects()->max('position') + 1,
        ]);
        $chat = $project->chats()->create(['user_id' => $user->id, 'position' => 0, 'content' => '']);
        $this->activeChatId = $chat->id;
    }

    public function renameProject(int $id, string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }
        $this->userProject($id)->update(['name' => $name]);
    }

    public function deleteProject(int $id): void
    {
        $user = Auth::user();
        if ($user->projects()->count() <= 1) {
            return; // always keep at least one project
        }
        $this->userProject($id)->delete();
        $this->activeChatId = $user->chats()->orderBy('id')->value('id');
        $this->ensureChat();
    }

    /* ---------------- Chats ---------------- */

    public function addChat(int $projectId): void
    {
        $project = $this->userProject($projectId);
        $project->update(['collapsed' => false]);
        $chat = $project->chats()->create([
            'user_id' => Auth::id(),
            'position' => (int) $project->chats()->max('position') + 1,
            'content' => '',
        ]);
        $this->activeChatId = $chat->id;
    }

    public function renameChat(int $id, string $title): void
    {
        $chat = $this->userChat($id);
        $title = trim($title);
        $chat->update(['title' => $title === '' ? null : $title]);
    }

    public function deleteChat(int $id): void
    {
        $chat = $this->userChat($id);
        $chat->delete();

        if ($this->activeChatId === $id) {
            $this->activeChatId = Auth::user()->chats()->orderBy('id')->value('id');
        }
        $this->ensureChat();
    }

    /* ---------------- Paste ---------------- */

    public function paste(string $text): void
    {
        if (trim($text) === '') {
            return;
        }

        $active = $this->activeChat();

        if ($active && trim((string) $active->content) === '') {
            $active->update(['content' => $text]);
            return;
        }

        // Otherwise start a new chat in the active chat's project.
        $projectId = $active?->project_id
            ?? Auth::user()->projects()->orderBy('position')->value('id');

        $project = $this->userProject($projectId);
        $chat = $project->chats()->create([
            'user_id' => Auth::id(),
            'position' => (int) $project->chats()->max('position') + 1,
            'content' => $text,
        ]);
        $this->activeChatId = $chat->id;
    }

    public function clearActive(): void
    {
        $this->activeChat()?->update(['content' => '']);
    }

    /* ---------------- Helpers ---------------- */

    private function ensureChat(): void
    {
        $user = Auth::user();
        if ($user->chats()->count() === 0) {
            $project = $user->projects()->orderBy('position')->first()
                ?? $user->projects()->create(['name' => 'My Project']);
            $chat = $project->chats()->create(['user_id' => $user->id, 'content' => '']);
            $this->activeChatId = $chat->id;
        }
    }

    private function activeChat(): ?Chat
    {
        return $this->activeChatId ? Auth::user()->chats()->find($this->activeChatId) : null;
    }

    private function ownsChat(int $id): bool
    {
        return Auth::user()->chats()->whereKey($id)->exists();
    }

    private function userChat(int $id): Chat
    {
        return Auth::user()->chats()->findOrFail($id);
    }

    private function userProject(int $id): Project
    {
        return Auth::user()->projects()->findOrFail($id);
    }

    public function render(MarkdownService $markdown)
    {
        $projects = Auth::user()->projects()->with('chats')->get();
        $active = $this->activeChat();

        $html = $active
            ? $markdown->toHtml((string) $active->content, $this->fixEncoding)
            : '';

        return view('livewire.reader', [
            'projects' => $projects,
            'active' => $active,
            'renderedHtml' => $html,
        ]);
    }
}
