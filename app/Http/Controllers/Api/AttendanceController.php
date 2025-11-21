<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    // Cek Absen Masuk
    public function checkIn(Request $request)
    {
        // 1. Validasi Input Koordinat
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
        $now = Carbon::now();

        // 2. Cek Jadwal Hari Ini
        $schedule = Schedule::where('user_id', $user->id)
            ->where('day_of_week', $today)
            ->first();

        if (! $schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal kerja untuk Anda hari ini.',
            ], 400);
        }

        // 3. Cek Apakah Sudah Absen Masuk?
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->toDateString())
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini.',
            ], 400);
        }

        // 4. Validasi Jarak (Geofencing) - Hanya jika TIDAK WFH
        if (! $schedule->is_wfh) {
            $school = SchoolSetting::first();
            if (! $school) {
                return response()->json(['message' => 'Pengaturan lokasi sekolah belum diset.'], 500);
            }

            $distance = $this->calculateDistance(
                $request->latitude, $request->longitude,
                $school->latitude, $school->longitude
            );

            // Konversi radius meter ke km untuk perbandingan
            $maxRadiusKm = $school->radius_meters / 1000;

            if ($distance > $maxRadiusKm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jarak terlalu jauh! Anda berjarak '.round($distance * 1000).' meter dari sekolah.',
                ], 400);
            }
        }
        // Jika WFH (is_wfh = true), cek lokasi dilewati.

        // 5. Simpan Absensi
        // Cek status terlambat (misal toleransi 15 menit)
        $scheduleTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
        // Terlambat jika waktu sekarang > (jam mulai jadwal + 15 menit)
        $status = $now->greaterThan($scheduleTime->addMinutes(15)) ? 'late' : 'present';

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'date' => $now->toDateString(),
            'check_in_time' => $now->toTimeString(),
            'check_in_lat' => $request->latitude,
            'check_in_long' => $request->longitude,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen Masuk Berhasil!',
            'data' => $attendance,
        ]);
    }

    // Absen Pulang
    public function checkOut(Request $request)
    {
        // 1. Validasi Input Koordinat
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::now()->dayOfWeekIso;
        $now = Carbon::now();

        // 2. Cek Absensi Masuk Hari Ini
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $now->toDateString())
            ->whereNull('check_out_time') // Pastikan belum absen pulang
            ->first();

        if (! $attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 400);
        }

        // 3. Cek Jadwal untuk validasi WFH
        $schedule = Schedule::find($attendance->schedule_id);

        if (! $schedule) {
            return response()->json(['message' => 'Jadwal terkait tidak ditemukan.'], 500);
        }

        // 4. Validasi Jarak (Geofencing) - Hanya jika TIDAK WFH
        if (! $schedule->is_wfh) {
            $school = SchoolSetting::first();
            if (! $school) {
                return response()->json(['message' => 'Pengaturan lokasi sekolah belum diset.'], 500);
            }

            $distance = $this->calculateDistance(
                $request->latitude, $request->longitude,
                $school->latitude, $school->longitude
            );

            $maxRadiusKm = $school->radius_meters / 1000;

            if ($distance > $maxRadiusKm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jarak terlalu jauh! Anda berjarak '.round($distance * 1000).' meter dari sekolah.',
                ], 400);
            }
        }
        // Jika WFH (is_wfh = true), cek lokasi dilewati.

        // 5. Update Absensi Pulang
        $attendance->update([
            'check_out_time' => $now->toTimeString(),
            'check_out_lat' => $request->latitude,
            'check_out_long' => $request->longitude,
            // Status tidak diubah saat check-out, hanya saat check-in
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen Pulang Berhasil!',
            'data' => $attendance,
        ]);
    }

    // Fungsi Rumus Haversine (Hitung Jarak dalam KM)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius bumi km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
