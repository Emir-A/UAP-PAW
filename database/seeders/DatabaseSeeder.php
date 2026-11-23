<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $path = database_path('timetable.json');
        if (! file_exists($path)) {
            return;
        }

        $data = json_decode(
            file_get_contents($path),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($data) {
            foreach ($data['rooms'] ?? [] as $room) {
                DB::table('rooms')->insertOrIgnore(
                    collect($room)->only(['code', 'building', 'floor', 'capacity'])->all()
                );
            }

            $oldCodes = ['F3.10a', 'F3.10b'];
            DB::table('course_sessions')->whereIn('room', $oldCodes)->update(['room' => 'F3.10']);
            DB::table('rooms')->whereIn('code', $oldCodes)->delete();

            foreach ($data['sessions'] ?? [] as $row) {
                $roomCode = in_array($row['room'], $oldCodes, true)
                    ? 'F3.10'
                    : $row['room'];

                DB::table('course_sessions')->insertOrIgnore([
                    'id' => $row['id'],
                    'room' => $roomCode,
                    'weekday' => $row['weekday'],
                    'title' => $row['title'],
                    'start_time' => $row['startTime'],
                    'end_time' => $row['endTime'],
                    'program' => $row['program'],
                    'class_group' => $row['group'],
                    'lecturer' => $row['lecturer'],
                ]);
            }
        });
    }
}