<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\Hospital;
use App\Models\User;
use App\Models\VaccinationRecord;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                'parents' => User::where('role', 'parent')->count(),
                'children' => Child::count(),
                'hospitals' => Hospital::count(),
                'pending' => Appointment::where('status', 'Pending')->count(),
                'vaccinations' => VaccinationRecord::count(),
            ],
            'upcoming' => Appointment::where('appointment_date', '>=', today())
                ->with(['child', 'hospital', 'vaccine'])
                ->orderBy('appointment_date')
                ->take(10)
                ->get(),
        ]);
    }

    public function children()
    {
        return view('admin.children', [
            'children' => Child::with('parent')->latest()->get(),
        ]);
    }

    public function vaccines()
    {
        return view('admin.vaccines', [
            'vaccines' => Vaccine::latest()->get(),
        ]);
    }

    public function storeVaccine(Request $r)
    {
        Vaccine::create($r->validate([
            'name' => 'required|max:100',
            'dose' => 'required|max:50',
            'age_months' => 'required|integer|min:0',
            'description' => 'nullable|max:500',
            'status' => 'required|in:Available,Unavailable',
        ]));

        return back()->with('success', 'Vaccine added.');
    }

    public function editVaccine(Vaccine $vaccine)
    {
        return view('admin.vaccine-edit', compact('vaccine'));
    }

    public function updateVaccine(Request $r, Vaccine $vaccine)
    {
        $vaccine->update($r->validate([
            'name' => 'required|max:100',
            'dose' => 'required|max:50',
            'age_months' => 'required|integer|min:0',
            'description' => 'nullable|max:500',
            'status' => 'required|in:Available,Unavailable',
        ]));

        return back()->with('success', 'Vaccine updated.');
    }

    public function deleteVaccine(Vaccine $vaccine)
    {
        $vaccine->delete();
        return back();
    }

    public function hospitals()
    {
        return view('admin.hospitals', [
            'hospitals' => Hospital::with('user')->latest()->get(),
        ]);
    }

    public function storeHospital(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
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

        $u = User::create([
            'name' => $d['name'],
            'email' => $d['email'],
            'password' => $d['password'],
            'role' => 'hospital',
            'phone' => $d['phone'] ?? null,
            'address' => $d['address'],
        ]);

        Hospital::create([
            'user_id' => $u->id,
            'name' => $d['name'],
            'phone' => $d['phone'] ?? null,
            'address' => $d['address'],
            'location' => $d['location'] ?? null,
            'license_no' => $d['license_no'] ?? null,
            'consultation_fee' => $d['consultation_fee'],
            'vaccination_fee' => $d['vaccination_fee'],
            'opening_hours' => $d['opening_hours'] ?? null,
            'facilities' => $d['facilities'] ?? null,
            'vaccine_status' => $d['vaccine_status'],
        ]);

        return back()->with('success', 'Hospital added.');
    }

    public function editHospital(Hospital $hospital)
    {
        return view('admin.hospital-edit', compact('hospital'));
    }

    public function updateHospital(Request $r, Hospital $hospital)
    {
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

        $hospital->update($d);

        $hospital->user->update([
            'name' => $d['name'],
            'phone' => $d['phone'] ?? null,
            'address' => $d['address'],
        ]);

        return redirect()
            ->route('admin.hospitals')
            ->with('success', 'Hospital updated.');
    }

    public function deleteHospital(Hospital $hospital)
    {
        $hospital->delete();
        return back();
    }

    public function bookings()
    {
        return view('admin.bookings', [
            'appointments' => Appointment::with([
                'child.parent',
                'hospital',
                'vaccine',
            ])->latest()->get(),
        ]);
    }

    public function updateBooking(Request $r, Appointment $appointment)
    {
        $appointment->update($r->validate([
            'status' => 'required|in:Approved,Rejected,Completed,Pending',
            'admin_note' => 'nullable|max:500',
        ]));

        return back()->with('success', 'Booking updated.');
    }

    public function reports(Request $r)
    {
        $records = VaccinationRecord::with([
            'child.parent',
            'vaccine',
            'hospital',
        ])
            ->when($r->filled('date'), fn ($q) =>
                $q->whereDate('given_on', $r->date)
            )
            ->latest('given_on')
            ->get();

        return view('admin.reports', compact('records'));
    }
}
