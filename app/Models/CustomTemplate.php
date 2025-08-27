<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'html_content',
        'css_content',
        'user_id',
        'is_public',
        'is_active',
        'thumbnail',
        'tags',
        'version',
        'parent_template_id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created this template
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent template if this is a variation
     */
    public function parentTemplate()
    {
        return $this->belongsTo(CustomTemplate::class, 'parent_template_id');
    }

    /**
     * Get variations of this template
     */
    public function variations()
    {
        return $this->hasMany(CustomTemplate::class, 'parent_template_id');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public templates
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for templates by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for templates by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the full HTML content with CSS
     */
    public function getFullHtmlAttribute()
    {
        return "<!DOCTYPE html>\n<html>\n<head>\n<meta charset=\"UTF-8\">\n<title>{$this->name}</title>\n<style>\n{$this->css_content}\n</style>\n</head>\n<body>\n{$this->html_content}\n</body>\n</html>";
    }

    /**
     * Get template preview (first 200 characters of HTML)
     */
    public function getPreviewAttribute()
    {
        $html = strip_tags($this->html_content);
        return strlen($html) > 200 ? substr($html, 0, 200) . '...' : $html;
    }

    /**
     * Check if template is owned by user
     */
    public function isOwnedBy($userId)
    {
        return $this->user_id == $userId;
    }

    /**
     * Check if template can be edited by user
     */
    public function canBeEditedBy($userId)
    {
        return $this->isOwnedBy($userId) || $this->is_public;
    }

    /**
     * Create a new version of this template
     */
    public function createVersion($data)
    {
        return $this->variations()->create([
            'name' => $data['name'] ?? $this->name . ' (Copy)',
            'description' => $data['description'] ?? $this->description,
            'category' => $data['category'] ?? $this->category,
            'html_content' => $data['html_content'] ?? $this->html_content,
            'css_content' => $data['css_content'] ?? $this->css_content,
            'user_id' => $data['user_id'] ?? $this->user_id,
            'is_public' => $data['is_public'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'parent_template_id' => $this->id,
            'version' => ($this->variations()->count() + 1)
        ]);
    }

    /**
     * Get template statistics
     */
    public function getStatsAttribute()
    {
        return [
            'variations_count' => $this->variations()->count(),
            'total_usage' => 0, // You can implement usage tracking
            'last_modified' => $this->updated_at->diffForHumans(),
            'created_days_ago' => $this->created_at->diffInDays(now())
        ];
    }
}
