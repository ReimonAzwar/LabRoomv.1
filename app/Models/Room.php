<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'nama_ruang', 'cap', 'fasilitas', 'status', 'closedUntil'];

    public function getNamaRuangAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function setNamaRuangAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getStatusAttribute($value)
    {
        $map = [
            'available' => 'dibuka',
            'maintenance' => 'maintenance',
            'closed' => 'ditutup'
        ];
        return $map[$value] ?? $value;
    }

    public function setStatusAttribute($value)
    {
        $map = [
            'dibuka' => 'available',
            'maintenance' => 'maintenance',
            'ditutup' => 'closed'
        ];
        $this->attributes['status'] = $map[$value] ?? $value;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
