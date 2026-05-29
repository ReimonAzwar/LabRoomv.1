<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::updateOrCreate(
            ['key' => 'system_name'],
            ['value' => 'Sisiru', 'description' => 'Nama website/sistem reservasi']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'whatsapp_notification'],
            ['value' => 'true', 'description' => 'Aktifkan notifikasi otomatis WhatsApp (true/false)']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'operational_hours_start'],
            ['value' => '00:00', 'description' => 'Jam operasional mulai']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'operational_hours_end'],
            ['value' => '23:59', 'description' => 'Jam operasional selesai']
        );
        SystemSetting::updateOrCreate(
            ['key' => 'contact_phone'],
            ['value' => '(0561) 736180', 'description' => 'Nomor telepon kontak yang ditampilkan di halaman depan']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'contact_email'],
            ['value' => 'kampus@polnep.ac.id', 'description' => 'Email kontak yang ditampilkan di halaman depan']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'contact_address'],
            ['value' => 'Jl. Jenderal Ahmad Yani, Bansir Laut, Pontianak Tenggara, Pontianak, Kalimantan Barat', 'description' => 'Alamat lengkap lab']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'lab_capacity_info'],
            ['value' => 'Lab: 20–30 orang · Seminar: 60 orang', 'description' => 'Informasi kapasitas ruangan']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'gmaps_iframe_url'],
            ['value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.814324976773!2d109.3444!3d-0.0558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e1d596e1b655555%3A0x6e9f2255018612!2sPoliteknik%20Negeri%20Pontianak!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid', 'description' => 'URL Embed Google Maps iframe']
        );
    }
}
