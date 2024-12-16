<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // get appointments
    public function index()
    {
        $user = User::with('appointments')->find(Auth::id());
        $userTimezone = $user->preferred_timezone; // Ambil timezone pengguna

        // Ambil appointment dan konversi waktu sesuai timezone pengguna
        $appointments = $user->appointments()
            ->with(['creator', 'participants' => function ($query) use ($user) {
                // Pilih semua peserta selain user login
                $query->where('user_id', '!=', $user->id);
            }])
            ->select('appointments.id as id', 'title', 'creator_id', 'start', 'end')
            ->get()
            ->map(function ($appointment) use ($userTimezone) {
                // Konversi waktu start dan end ke timezone pengguna
                $appointment->start = Carbon::parse($appointment->start, 'UTC')->setTimezone($userTimezone)->format('H:i');
                $appointment->end = Carbon::parse($appointment->end, 'UTC')->setTimezone($userTimezone)->format('H:i');
                return $appointment;
            });

        return response()->json(['data' => $appointments],200);
    }

    // simpan appointment ke database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'participant_id' => 'required|exists:users,id',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
        ]);

        $creator = Auth::user(); // Pengguna yang membuat appointment
        $participant = User::findOrFail($validatedData['participant_id']); // Peserta

        // Konversi waktu input ke UTC
        $startUtc = Carbon::createFromFormat('H:i', $validatedData['start'], $creator->preferred_timezone)->setTimezone('UTC');
        $endUtc = Carbon::createFromFormat('H:i', $validatedData['end'], $creator->preferred_timezone)->setTimezone('UTC');


        // Validasi rentang waktu jam kerja pengguna yang membuat appointment
        $creatorWorkStart = Carbon::createFromTimeString('08:00', $creator->preferred_timezone)->setTimezone('UTC');
        $creatorWorkEnd = Carbon::createFromTimeString('17:00', $creator->preferred_timezone)->setTimezone('UTC');

        if ($startUtc->lt($creatorWorkStart) || $endUtc->gt($creatorWorkEnd)) {
            return response()->json([
                'message' => 'Waktu appointment tidak sesuai dengan jam kerja Anda (08:00:00 - 17:00:00).'
            ], 422);
        }

        // Validasi rentang waktu jam kerja peserta
        $participantWorkStart = Carbon::createFromTimeString('08:00', $participant->preferred_timezone)->setTimezone('UTC');
        $participantWorkEnd = Carbon::createFromTimeString('17:00', $participant->preferred_timezone)->setTimezone('UTC');

        if ($startUtc->lt($participantWorkStart) || $endUtc->gt($participantWorkEnd)) {
            return response()->json([
                'message' => 'Waktu appointment tidak sesuai dengan jam kerja peserta ('.$participantWorkStart->setTimezone($creator->preferred_timezone)->format('H:i:s').' - '.$participantWorkEnd->setTimezone($creator->preferred_timezone)->format('H:i:s').').'
            ], 422);
        }

        // Validasi apakah waktu mulai lebih kecil dari waktu selesai
        if ($startUtc->gte($endUtc)) {
            return response()->json([
                'message' => 'Waktu mulai harus lebih kecil dari waktu selesai.'
            ], 422);
        }

        // return $startUtc->format('H:i');

        $appointment = Appointment::create([
            'title' => $validatedData['title'],
            'start' => $startUtc->format('H:i'),
            'end' => $endUtc->format('H:i'),
            'creator_id' => Auth::id(),
        ]);

        $appointment->participants()->attach([$appointment->creator_id, $request->participant_id]);

        return response()->json(['message' => 'Appointment berhasil ditambahkan.'],201);
    }
}
