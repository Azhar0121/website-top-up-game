<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'whatsapp',
        'subject',
        'message',
        'image_path',
        'status',
        'handled_by',
        'admin_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Baru',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            default => ucfirst($this->status),
        };
    }
}
