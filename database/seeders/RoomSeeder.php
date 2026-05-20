<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            ['name' => 'Laboratorium Komunikasi dan Multimedia A', 'cap' => 40, 'fasilitas' => 'PC, AC, Proyektor, WiFi', 'status' => 'available'],
            ['name' => 'Laboratorium Komunikasi dan Multimedia B', 'cap' => 40, 'fasilitas' => 'PC, AC, Proyektor, WiFi', 'status' => 'available'],
            ['name' => 'Laboratorium Data Intelligence A', 'cap' => 40, 'fasilitas' => 'PC, AC, Proyektor, WiFi', 'status' => 'available'],
            ['name' => 'Laboratorium Data Intelligence B', 'cap' => 40, 'fasilitas' => 'PC, AC, Proyektor, WiFi', 'status' => 'available'],
            ['name' => 'Ruang Diskusi', 'cap' => 20, 'fasilitas' => 'AC, Meja Diskusi, WiFi', 'status' => 'available'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
