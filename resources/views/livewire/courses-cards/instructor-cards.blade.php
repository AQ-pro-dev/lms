<div class="swiper">
    <div class="arrows d-flex align-items-center justify-content-end gap-2">
        <div class="swiper-button-prev position-relative"></div>
        <div class="swiper-button-next position-relative"></div>
    </div>
    <div class="swiper-wrapper">
        @foreach ($featuredTutors as $tutor)
            <div class="swiper-slide">
                <a href="{{ route('tutor.profile', $tutor->user_id) }}">
                    <div class="instructor-card shadow">
                        <ul class="d-flex gap-4">
                            <li class="instructor-img my-3">
                                <img src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="Instructor Image">
                            </li>
                            <li class="card-details">
                                <h5>{{ $tutor->user->first_name }} {{ $tutor->user->last_name }}</h5>
                                <p style="height: 30px" class="instructor-text my-2">{{ $tutor->specialization }}</p>
                                {{-- <div class="instructor-rating d-flex">
                                    <span>4.5</span>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    <span>Instructor rating</span>
                                </div> --}}
                                <div class="instructor-data d-flex flex-column my-2">
                                    {{-- <span class="instructor-data-span"><strong>0</strong> students</span> --}}
                                    <span
                                        class="instructor-data-span"><strong>{{ $tutor->user->courses()->count() }}</strong>
                                        courses</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
