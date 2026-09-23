<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalController extends Controller
{
    private function hospital()
    {
        return Auth::user()->hospital()->firstOrFail();
    }

    public function dashboard()
    {
        $h = $this->hospital();

        return view('hospital.dashboard', [
            'hospital' => $h,
            'appointments' => $h->appointments()
                ->with(['child', 'vaccine'])
                ->latest()
                ->take(10)
                ->get(),
            'stats' => [
                'approved' => $h->appointments()->where('status', 'Approved')->count(),
                'completed' => $h->appointments()->where('status', 'Completed')->count(),
            ],
        ]);
    }

    public function profile()
    {
        return view('hospital.profile', [
            'hospital' => $this->hospital(),
        ]);
    }

    public function updateProfile(Request $r)
    {
        $h = $this->hospital();

        $d = $r->validate([
            'name' => 'required|max:120',
            'phone' => 'nullable|max:30',
            'address' => 'required|max:255',
            'location' => 'nullable|max:255',
            'license_no' => 'nullable|max:80',
            'consultation_fee' => 'required|numeric|min:0',
            'vaccination_fee' => 'required|numeric|min:0',
            'opening_hours' => 'nullable|max:120',
            'facilities' => 'nullable|max:500',
            'vaccine_status' => 'required|in:Available,Unavailable',
        ]);

        $h->update($d);

        $h->user->update([
            'name' => $d['name'],
            'phone' => $d['phone'] ?? null,
            'address' => $d['address'],
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function appointments()
    {
        return view('hospital.appointments', [
            'appointments' => $this->hospital()
                ->appointments()
                ->with(['child.parent', 'vaccine'])
                ->latest()
                ->get(),
        ]);
    }

    public function complete(Request $r, Appointment $appointment)
    {
        $h = $this->hospital();

        abort_unless($appointment->hospital_id === $h->id, 403);

        $d = $r->validate([
            'status' => 'required|in:Completed,Not Vaccinated',
            'hospital_note' => 'nullable|max:500',
        ]);

        $appointment->update($d);

        if ($d['status'] === 'Completed') {
            VaccinationRecord::updateOrCreate(
                [
                    'child_id' => $appointment->child_id,
                    'vaccine_id' => $appointment->vaccine_id,
                    'given_on' => $appointment->appointment_date,
                ],
                [
                    'hospital_id' => $h->id,
                    'dose_no' => $appointment->vaccine->dose,
                    'status' => 'Vaccinated',
                    'remarks' => $d['hospital_note'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Vaccination status updated.');
    }
}
