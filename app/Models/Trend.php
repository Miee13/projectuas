<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trend extends Model
{
    use HasFactory;

    /**
     * Menonaktifkan proteksi mass assignment.
     * agar Seeder bisa memasukkan data ke database.
     */
    protected $guarded = [];

    /**
     * Casting atribut.
     * Ini memberitahu Laravel bahwa kolom 'news_links' adalah JSON
     * dan harus diperlakukan sebagai array di PHP.
     */
    protected $casts = [
        'news_links' => 'array',
    ];
}