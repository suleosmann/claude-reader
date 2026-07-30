<?php

namespace App\Models;

use App\Services\MarkdownService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Chat extends Model
{
    protected $fillable = ['user_id', 'project_id', 'title', 'content', 'position'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Title to show: the user-set title, otherwise derived from the first
     * meaningful line of the (encoding-fixed) content.
     */
    public function displayTitle(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        $fixed = app(MarkdownService::class)->fixEncoding($this->content ?? '');

        foreach (preg_split('/\r\n|\r|\n/', $fixed) as $line) {
            $clean = trim(preg_replace('/^#{1,6}\s+/', '', $line));
            $clean = trim(preg_replace('/[*_`>#-]/', '', $clean));
            if ($clean !== '') {
                return Str::limit($clean, 30, '…');
            }
        }

        return 'Untitled';
    }
}
