@extends('layouts.app')

@section('top_title', 'Find Hospital')

@section('content')
<style>
    .ai-plan{display:flex;align-items:center;gap:14px;padding:15px 18px;margin:-2px 0 18px;border:1px solid #b7dcff;border-radius:16px;background:linear-gradient(100deg,#eff8ff,#f2fffc);box-shadow:var(--shadow)}
    .ai-orb{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:13px;background:linear-gradient(135deg,#2563eb,#06b6d4);color:#fff;font-size:18px}
    .ai-copy{display:flex;flex-direction:column;gap:3px;font-size:11px;color:#52677f}
    .ai-copy strong{font-size:13px;color:#153354}
    .ai-label{font-size:9px;font-weight:850;letter-spacing:.1em;text-transform:uppercase;color:#1775b9}
    .ai-privacy{margin-left:auto;color:#347090;font-size:10px;white-space:nowrap}
    @media(max-width:600px){.ai-plan{align-items:flex-start}.ai-privacy{display:none}}

    .eyebrow{display:inline-flex;gap:6px;color:#1677ef;font-size:10px;font-weight:850;text-transform:uppercase;letter-spacing:.1em;margin-bottom:9px}
    .directory-count{background:#eaf3ff;border:1px solid #d6e8ff;color:#225da8;border-radius:12px;padding:10px 13px;font-size:11px;white-space:nowrap}
    .directory-count strong{font-size:17px;margin-right:4px}
    .directory-toolbar{background:#fff;border:1px solid var(--border);border-radius:15px;padding:13px 15px;margin-bottom:19px;box-shadow:var(--shadow)}
    .directory-toolbar .search-row{margin:0}
    .search-input{position:relative;flex:1}
    .search-input>i{position:absolute;left:13px;top:12px;color:#7b8ba0;z-index:1}
    .search-input .input{padding-left:36px}
    .directory-note{font-size:11px;color:#64748b;margin-top:10px}
    .directory-note i{color:#2563eb;margin-right:4px}
    .hospital-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:18px}
    .hospital-card{background:#fff;border:1px solid var(--border);border-radius:18px;padding:20px;box-shadow:var(--shadow);transition:.18s}
    .hospital-card:hover{border-color:#bcd7ff;box-shadow:0 16px 35px rgba(30,64,175,.09);transform:translateY(-2px)}
    .hospital-card-top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
    .hospital-identity{display:flex;gap:11px}
    .hospital-icon{width:43px;height:43px;border-radius:13px;display:grid;place-items:center;background:linear-gradient(135deg,#e5f1ff,#d6f7fa);color:#1369c7;font-size:19px}
    .hospital-identity h2{font-size:15px;letter-spacing:-.02em;margin:1px 0 5px}
    .hospital-identity p{font-size:11px;color:var(--muted);margin:0}
    .hospital-details{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:17px 0 11px}
    .hospital-details>div{background:#f8fafc;border-radius:10px;padding:10px}
    .hospital-details span,.fee-panel span{font-size:10px;color:#718096;display:block;margin-bottom:5px}
    .hospital-details strong{font-size:11px;line-height:1.35;display:block}
    .fee-panel{display:grid;grid-template-columns:1fr 1fr;border:1px solid #dceafb;border-radius:11px;overflow:hidden;background:#f5f9ff}
    .fee-panel>div{padding:11px 12px}
    .fee-panel>div+div{border-left:1px solid #dceafb}
    .fee-panel strong{font-size:15px;color:#164b9a}
    .facility-line,.hospital-address{display:flex;gap:8px;font-size:11px;color:#58697e;margin-top:12px;line-height:1.5}
    .facility-line i{color:#b7791f}
    .hospital-address i{color:#e24d4d}
    .booking-form{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:17px;padding-top:16px;border-top:1px solid #edf1f6}
    .booking-submit{grid-column:1/-1;margin-top:2px}
    @media(max-width:500px){.hospital-grid{grid-template-columns:1fr}.hospital-card-top{flex-direction:column}.booking-form{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div>
        <div class="eyebrow"><i class="bi bi-shield-check"></i> Verified vaccination network</div>
        <h1 class="page-title">Find the right vaccination centre</h1>
        <div class="page-subtitle">Compare fees, services and availability before requesting an appointment.</div>
    </div>

    <div class="directory-count">
        <strong>{{ $h->count() }}</strong> centres available
    </div>
</div>

<section class="directory-toolbar">
    <form class="search-row" method="GET" action="{{ route('parent.hospitals') }}">
        <div class="search-input">
            <i class="bi bi-search"></i>
            <input class="input" name="q" value="{{ $q }}" placeholder="Search by hospital, area or address">
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-search"></i> Search centres
        </button>
    </form>

    <div class="directory-note">
        <i class="bi bi-info-circle"></i> Charges shown are the hospital's current listed fees.
    </div>
</section>

@if($aiPlan)
    <section class="ai-plan">
        <div class="ai-orb"><i class="bi bi-cpu-fill"></i></div>

        <div class="ai-copy">
            <div class="ai-label">VaxMatch AI · personal schedule assistant</div>
            <strong>{{ $aiPlan['child']->name }} is {{ $aiPlan['age'] }} months old.</strong>
            <span>
                @if($aiPlan['next'])
                    Suggested next dose: <b>{{ $aiPlan['next']->name }} — {{ $aiPlan['next']->dose }}</b>.
                @else
                    Vaccination record looks up to date.
                @endif

                @if($aiPlan['recommended'])
                    Best-value centre from currently listed options: <b>{{ $aiPlan['recommended']->name }}</b>.
                @endif
            </span>
        </div>

        <div class="ai-privacy">
            <i class="bi bi-shield-lock"></i> Private, on-device matching
        </div>
    </section>
@else
    <section class="ai-plan">
        <div class="ai-orb"><i class="bi bi-cpu-fill"></i></div>
        <div class="ai-copy">
            <div class="ai-label">VaxMatch AI · personal schedule assistant</div>
            <strong>Add a child to unlock a personalized vaccination plan.</strong>
            <span>We match the child’s age with the available schedule and help find a suitable centre.</span>
        </div>
    </section>
@endif

<div class="hospital-grid">
    @forelse($h as $hospital)
        <section class="hospital-card">
            <div class="hospital-card-top">
                <div class="hospital-identity">
                    <span class="hospital-icon">
                        <i class="bi bi-hospital-fill"></i>
                    </span>

                    <div>
                        <h2>{{ $hospital->name }}</h2>
                        <p>
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ $hospital->location ?: $hospital->address }}
                        </p>
                    </div>
                </div>

                <span class="badge badge-success">
                    <i class="bi bi-check-circle-fill"></i> Open for bookings
                </span>
            </div>

            <div class="hospital-details">
                <div>
                    <span><i class="bi bi-clock"></i> Hours</span>
                    <strong>{{ $hospital->opening_hours ?: 'Call for hours' }}</strong>
                </div>

                <div>
                    <span><i class="bi bi-telephone"></i> Contact</span>
                    <strong>{{ $hospital->phone ?: 'Contact hospital' }}</strong>
                </div>
            </div>

            <div class="fee-panel">
                <div>
                    <span>Consultation fee</span>
                    <strong>Rs. {{ number_format((float) $hospital->consultation_fee, 0) }}</strong>
                </div>

                <div>
                    <span>Vaccination service</span>
                    <strong>Rs. {{ number_format((float) $hospital->vaccination_fee, 0) }}</strong>
                </div>
            </div>

            @if($hospital->facilities)
                <div class="facility-line">
                    <i class="bi bi-stars"></i>
                    <span>{{ $hospital->facilities }}</span>
                </div>
            @endif

            <div class="hospital-address">
                <i class="bi bi-geo-alt"></i>
                {{ $hospital->address }}
            </div>

            <form method="POST" action="{{ route('parent.book') }}" class="booking-form">
                @csrf

                <input type="hidden" name="hospital_id" value="{{ $hospital->id }}">

                <div class="field">
                    <label class="label">Child</label>
                    <select class="select" name="child_id" required>
                        <option value="">Select child</option>
                        @foreach($children as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label">Vaccine</label>
                    <select class="select" name="vaccine_id" required>
                        <option value="">Select vaccine</option>
                        @foreach($vaccines as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} — {{ $v->dose }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label">Date</label>
                    <input class="input" name="appointment_date" type="date" min="{{ today()->format('Y-m-d') }}" required>
                </div>

                <div class="field">
                    <label class="label">Time</label>
                    <input class="input" name="appointment_time" type="time" required>
                </div>

                <button class="btn btn-primary booking-submit">
                    <i class="bi bi-calendar2-plus"></i> Request appointment
                </button>
            </form>
        </section>
    @empty
        <div class="panel">
            <div class="empty">
                <i class="bi bi-search" style="font-size:28px;display:block;margin-bottom:9px"></i>
                @if($q)
                    No available hospitals match your search for <strong>{{ $q }}</strong>.
                @else
                    No hospitals are currently available for vaccination bookings.
                @endif
            </div>
        </div>
    @endforelse
</div>
@endsection
