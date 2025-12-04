<div class="about-us">
    @push('title')
        About-us
    @endpush
    <section class="about-us-main" style="background-image: url('{{ asset('assets/images/Section.webp') }}');">
        <div class="container">
            <div class="content-wrapper">
            <div class="inner d-flex flex-column justify-content-center align-items-center">
                <h3>FREEDOM HOUSE RECOVERY CENTER</h3>
                <h1 class="my-3">WHO WE ARE</h1>
                <div class="description mt-2">
                    <p>We envision our community becoming self-sustaining, healthy and productive. We believe people can
                        live full, enriched lives in recovery.</p>
                </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about-us-body">
        <div class="container">
            <div class="inner">
                <div class="col">
                    <div class="row">
                        <h2>ABOUT US</h2>
                    </div>
                    <div class="row">
                        <div class="description">
                            <p>Freedom House Recovery Center is committed to a person-centered, strengths-based approach
                                to behavioral health programs and services. We promote a philosophy of respect for and
                                recognition of the individuality of each person. Our mission is to promote, enhance, and
                                support recovery for men, women and children affected by mental illness and substance
                                use by utilizing an evidence-based, comprehensive, and person-centered approach. Our
                                strategy is dedicated to the enhancement of personal growth and betterment. Freedom
                                House provides a full array of behavioral health services to support individuals on
                                their journey to living their best life. We are a non-profit organization that serves as
                                a “safety net” clinic. We are a Certified Community Behavioral Health Clinic (CCBHC)
                                certified by the Substance Abuse and Mental Health Services Administration (SAMHSA).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="training-team-section">
        <div class="container">
            <div class="row text-center">
                <div class="col">
                    <h3>MEET THE TRAINING TEAM</h3>
                    <p>Meet our trainers of Freedom House</p>
                </div>
            </div>

            <div class="card-container">
                @foreach ($tutors as $tutor)
                    <div class="card">
                        <div class="card-img-top">
                            {{-- <img
                                src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/image (3).webp') }}"> --}}
                            <img src="{{ asset('assets/images/dummy-profile-photo.webp') }}" alt="">
                        </div>
                        <div class="card-body text-center">
                            <h5>{{ $tutor->user->first_name . ' ' . $tutor->user->last_name }}</h5>
                            <span>{{ $tutor->specialization }}</span>
                            <p>{{ $tutor->user->bio }}</p>
                            <a href="{{ route('tutor.profile', $tutor->user_id) }}" class="" wire:navigate>Show
                                More</a>
                        </div>
                    </div>
                @endforeach
                {{-- <div class="card">
                    <img src="{{ asset('assets/images/image (1).assets/images/Section.webp') }}" alt="" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5>Autumn Green</h5>
                        <span>Behavioral Health Trainer</span>
                        <p>Autumn has worked as a mental health clinician for over 3 years. She began her career within
                            mental health 15 years ago.</p>
                        <a href="#" class="">Show More</a>
                    </div>
                </div>
                <div class="card">
                    <img src="{{ asset('assets/images/image.assets/images/Section.webp') }}" alt="" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5>Autumn Green</h5>
                        <span>Behavioral Health Trainer</span>
                        <p>Autumn has worked as a mental health clinician for over 3 years. She began her career within
                            mental health 15 years ago.</p>
                        <a href="#" class="">Show More</a>
                    </div>
                </div>
                <div class="card">
                    <img src="{{ asset('assets/images/image (1).assets/images/Section.webp') }}" alt="" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5>Autumn Green</h5>
                        <span>Behavioral Health Trainer</span>
                        <p>Autumn has worked as a mental health clinician for over 3 years. She began her career within
                            mental health 15 years ago.</p>
                        <a href="#" class="">Show More</a>
                    </div>
                </div>
                <div class="card">
                    <img src="{{ asset('assets/images/image (2).assets/images/Section.webp') }}" alt="" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5>Autumn Green</h5>
                        <span>Behavioral Health Trainer</span>
                        <p>Autumn has worked as a mental health clinician for over 3 years. She began her career within
                            mental health 15 years ago.</p>
                        <a href="#" class="">Show More</a>
                    </div>
                </div> --}}

            </div>
        </div>
    </section>
</div>
