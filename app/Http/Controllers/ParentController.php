<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\Hospital;
use App\Models\VaccinationRecord;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function dashboard()
    {
        $children = Child::where('parent_id', Auth::id())->get();

        $appointments = Appointment::whereHas('child', fn ($q) =>
            $q->where('parent_id', Auth::id())
        )
            ->with(['child', 'hospital', 'vaccine'])
            ->latest()
            ->take(8)
            ->get();

        return view('parent.dashboard', compact('children', 'appointments'));
    }

    public function children()
    {
        return view('parent.children', [
            'children' => Child::where('parent_id', Auth::id())
                ->latest()
                ->get(),
        ]);
    }

    public function storeChild(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:100',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|max:5',
            'guardian_phone' => 'nullable|max:30',
            'address' => 'nullable|max:255',
        ]);

        $d['parent_id'] = Auth::id();
        Child::create($d);

        return back()->with('success', 'Child added.');
    }

    public function updateChild(Request $r, Child $child)
    {
        abort_unless($child->parent_id === Auth::id(), 403);

        $child->update($r->validate([
            'name' => 'required|max:100',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|max:5',
            'guardian_phone' => 'nullable|max:30',
            'address' => 'nullable|max:255',
        ]));

        return back()->with('success', 'Child updated.');
    }

    public function destroyChild(Child $child)
    {
        abort_unless($child->parent_id === Auth::id(), 403);
        $child->delete();

        return back()->with('success', 'Child removed.');
    }

    /**
     * Find Hospital page.
     * Shows all hospitals currently accepting vaccination bookings.
     * Search works against hospital name, address and location.
     */
    public function hospitals(Request $r)
    {
        $q = trim((string) $r->get('q', ''));

        $h = Hospital::query()
            ->where('vaccine_status', 'Available')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($search) use ($q) {
                    $search->where('name', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->get();

        $children = Child::where('parent_id', Auth::id())
            ->latest()
            ->get();

        $vaccines = Vaccine::where('status', 'Available')
            ->orderBy('age_months')
            ->orderBy('name')
            ->get();

        $selected = $children->first();
        $aiPlan = null;

        if ($selected) {
            $age = Carbon::parse($selected->dob)->diffInMonths(today());

            $done = VaccinationRecord::where('child_id', $selected->id)
                ->pluck('vaccine_id');

            $next = $vaccines->first(fn ($v) =>
                $v->age_months <= $age && !$done->contains($v->id)
            );

            $recommended = $h->sortBy(fn ($hospital) =>
                (float) $hospital->consultation_fee + (float) $hospital->vaccination_fee
            )->first();

            $aiPlan = [
                'child' => $selected,
                'age' => $age,
                'next' => $next,
                'recommended' => $recommended,
            ];
        }

        return view('parent.hospitals', compact(
            'h',
            'children',
            'vaccines',
            'q',
            'aiPlan'
        ));
    }

    public function book(Request $r)
    {
        $d = $r->validate([
            'child_id' => 'required|exists:children,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        Child::where('id', $d['child_id'])
            ->where('parent_id', Auth::id())
            ->firstOrFail();

        Hospital::whereKey($d['hospital_id'])
            ->where('vaccine_status', 'Available')
            ->firstOrFail();

        Vaccine::whereKey($d['vaccine_id'])
            ->where('status', 'Available')
            ->firstOrFail();

        $d['status'] = 'Pending';
        Appointment::create($d);

        return back()->with('success', 'Appointment request submitted.');
    }

    public function appointments()
    {
        $appointments = Appointment::whereHas('child', fn ($q) =>
            $q->where('parent_id', Auth::id())
        )
            ->with(['child', 'hospital', 'vaccine'])
            ->latest()
            ->get();

        return view('parent.appointments', compact('appointments'));
    }

    public function records()
    {
        $records = VaccinationRecord::whereHas('child', fn ($q) =>
            $q->where('parent_id', Auth::id())
        )
            ->with(['child', 'vaccine', 'hospital'])
            ->latest('given_on')
            ->get();

        return view('parent.records', compact('records'));
    }

    public function profile()
    {
        return view('parent.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $r)
    {
        Auth::user()->update($r->validate([
            'name' => 'required|max:100',
            'phone' => 'nullable|max:30',
            'address' => 'nullable|max:255',
        ]));

        return back()->with('success', 'Profile updated.');
    }
}
