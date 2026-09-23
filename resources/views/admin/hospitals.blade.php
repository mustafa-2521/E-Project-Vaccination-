@extends('layouts.app')

@section('top_title', 'Hospitals')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Hospitals</h1>
        <div class="page-subtitle">Register vaccination centers and manage their availability.</div>
    </div>
</div>

<section class="form-card" style="padding:18px;margin-bottom:18px">
    <div class="panel-title" style="margin-bottom:14px">Register hospital</div>

    <form method="POST" action="{{ route('admin.hospitals.store') }}" class="form-grid">
        @csrf

        <div class="field">
            <label class="label">Hospital name</label>
            <input class="input" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="field">
            <label class="label">Login email</label>
            <input class="input" name="email" type="email" value="{{ old('email') }}" required>
        </div>

        <div class="field">
            <label class="label">Password</label>
            <input class="input" name="password" type="password" required>
        </div>

        <div class="field">
            <label class="label">Phone</label>
            <input class="input" name="phone" value="{{ old('phone') }}">
        </div>

        <div class="field full">
            <label class="label">Address</label>
            <input class="input" name="address" value="{{ old('address') }}" required>
        </div>

        <div class="field">
            <label class="label">Location / Area</label>
            <input class="input" name="location" value="{{ old('location') }}">
        </div>

        <div class="field">
            <label class="label">License number</label>
            <input class="input" name="license_no" value="{{ old('license_no') }}">
        </div>

        <div class="field">
            <label class="label">Consultation fee</label>
            <input class="input" name="consultation_fee" type="number" min="0" step="0.01" value="{{ old('consultation_fee', 0) }}" required>
        </div>

        <div class="field">
            <label class="label">Vaccination fee</label>
            <input class="input" name="vaccination_fee" type="number" min="0" step="0.01" value="{{ old('vaccination_fee', 0) }}" required>
        </div>

        <div class="field">
            <label class="label">Opening hours</label>
            <input class="input" name="opening_hours" value="{{ old('opening_hours') }}" placeholder="09:00 AM - 09:00 PM">
        </div>

        <div class="field">
            <label class="label">Facilities</label>
            <input class="input" name="facilities" value="{{ old('facilities') }}" placeholder="Pediatric care, cold-chain storage">
        </div>

        <div class="field">
            <label class="label">Vaccine availability</label>
            <select class="select" name="vaccine_status">
                <option value="Available" @selected(old('vaccine_status', 'Available') === 'Available')>Available</option>
                <option value="Unavailable" @selected(old('vaccine_status') === 'Unavailable')>Unavailable</option>
            </select>
        </div>

        <div class="form-actions full">
            <button class="btn btn-primary">
                <i class="bi bi-hospital"></i> Register hospital
            </button>
        </div>
    </form>
</section>

<section class="table-card">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Hospital</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Fees</th>
                    <th>Availability</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($hospitals as $h)
                    <tr>
                        <td>
                            <div class="person">
                                <span class="avatar"><i class="bi bi-hospital"></i></span>
                                <div>
                                    <strong>{{ $h->name }}</strong>
                                    <div class="muted" style="font-size:11px">{{ $h->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $h->phone ?: '—' }}</td>
                        <td>{{ $h->location ?: $h->address }}</td>
                        <td>
                            <div style="font-size:11px">Consultation: Rs. {{ number_format((float) $h->consultation_fee, 0) }}</div>
                            <div style="font-size:11px">Vaccination: Rs. {{ number_format((float) $h->vaccination_fee, 0) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $h->vaccine_status === 'Available' ? 'badge-success' : 'badge-danger' }}">
                                {{ $h->vaccine_status }}
                            </span>
                        </td>
                        <td style="display:flex;gap:6px">
                            <a class="btn btn-light" href="{{ route('admin.hospitals.edit', $h) }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.hospitals.delete', $h) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('Delete this hospital?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">No hospitals registered.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
