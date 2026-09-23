@extends('layouts.app')

@section('title', 'Sign in · VaccineCare')

@section('guest_content')

<div class="auth-page">

    <section class="auth-hero">

        <div class="brand" style="padding:0;margin-bottom:48px">

            <span class="brand-mark">
                <i class="bi bi-heart-pulse-fill"></i>
            </span>

            <span>
                <span class="brand-title">VaccineCare</span>
                <span class="brand-sub">Vaccination Management</span>
            </span>

        </div>

        <h1>Smarter vaccination management.</h1>

        <p>
            Manage children, vaccination schedules, hospitals and appointments
            from one secure platform.
        </p>

        <div class="hero-points">

            <div class="hero-point">
                <i class="bi bi-check2-circle"></i>
                Centralized vaccination records
            </div>

            <div class="hero-point">
                <i class="bi bi-check2-circle"></i>
                Parent, hospital and admin workflows
            </div>

            <div class="hero-point">
                <i class="bi bi-check2-circle"></i>
                Appointment tracking and reporting
            </div>

        </div>

    </section>


    <section class="auth-side">

        <div class="auth-card">

            <div style="font-size:12px;color:#2563eb;font-weight:800;margin-bottom:8px">
                WELCOME BACK
            </div>

            <h2>Sign in to your account</h2>

            <div class="sub">
                Enter your credentials to continue.
            </div>


            {{-- Login Error Message --}}

            @if ($errors->any())

                <div style="
                    margin:15px 0;
                    padding:12px 14px;
                    border:1px solid #fecaca;
                    border-radius:10px;
                    background:#fef2f2;
                    color:#b91c1c;
                    font-size:12px;
                    line-height:1.5;
                ">

                    <i class="bi bi-exclamation-circle-fill"></i>

                    {{ $errors->first() }}

                </div>

            @endif


            <form method="POST" action="{{ route('login.post') }}">

                @csrf

                <div class="field" style="margin-bottom:14px">

                    <label class="label">
                        Email address
                    </label>

                    <input
                        class="input"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                    >

                </div>


                <div class="field">

                    <label class="label">
                        Password
                    </label>

                    <input
                        class="input"
                        name="password"
                        type="password"
                        placeholder="••••••••"
                        required
                    >

                </div>


                <label style="
                    display:flex;
                    align-items:center;
                    gap:7px;
                    font-size:12px;
                    margin:13px 0;
                    color:#64748b;
                ">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    Remember me

                </label>


                <button
                    class="btn btn-primary"
                    style="width:100%;padding:12px"
                >

                    Sign in

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>


            <div style="
                text-align:center;
                margin-top:20px;
                font-size:13px;
                color:#64748b;
            ">

                New parent?

                <a
                    href="{{ route('register') }}"
                    style="
                        color:#2563eb;
                        font-weight:700;
                        text-decoration:none;
                    "
                >
                    Create an account
                </a>

            </div>


           

        </div>

    </section>

</div>

@endsection