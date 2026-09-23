@extends('layouts.app')

@section('top_title', 'Hospital Profile')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Hospital Profile</h1>
        <div class="page-subtitle">Keep your center information, fees and vaccine availability current.</div>
    </div>
</div>

<section class="form-card" style="padding:20px;max-width:900px">
    <form method="POST" action="{{ route('hospital.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="field">
                <label class="label">Hospital name</label>
                <input class="input" name="name" value="{{ $hospital->name }}" required>
            </div>

            <div class="field">
                <label class="label">Phone</label>
                <input class="input" name="phone" value="{{ $hospital->phone }}">
            </div>

            <div class="field full">
                <label class="label">Address</label>
                <input class="input" name="address" value="{{ $hospital->address }}" required>
            </div>

            <div class="field">
                <label class="label">Location</label>
                <input class="input" name="location" value="{{ $hospital->location }}">
            </div>

            <div class="field">
                <label class="label">License number</label>
                <input class="input" name="license_no" value="{{ $hospital->license_no }}">
            </div>

            <div class="field">
                <label class="label">Consultation fee</label>
                <input class="input" name="consultation_fee" type="number" min="0" step="0.01" value="{{ $hospital->consultation_fee ?? 0 }}" required>
            </div>

            <div class="field">
                <label class="label">Vaccination fee</label>
                <input class="input" name="vaccination_fee" type="number" min="0" step="0.01" value="{{ $hospital->vaccination_fee ?? 0 }}" required>
            </div>

            <div class="field">
                <label class="label">Opening hours</label>
                <input class="input" name="opening_hours" value="{{ $hospital->opening_hours }}">
            </div>

            <div class="field">
                <label class="label">Facilities</label>
                <input class="input" name="facilities" value="{{ $hospital->facilities }}">
            </div>

            <div class="field">
                <label class="label">Vaccine availability</label>
                <select class="select" name="vaccine_status">
                    <option value="Available" @selected($hospital->vaccine_status === 'Available')>Available</option>
                    <option value="Unavailable" @selected($hospital->vaccine_status === 'Unavailable')>Unavailable</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary"><i class="bi bi-check2"></i> Save profile</button>
        </div>
    </form>
</section>
@endsection
