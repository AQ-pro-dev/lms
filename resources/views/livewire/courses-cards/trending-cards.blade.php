<div class="swiper">
    <div class="arrows d-flex align-items-center justify-content-end gap-2">
        <div class="swiper-button-prev position-relative"></div>
        <div class="swiper-button-next position-relative"></div>
    </div>
    <div class="swiper-wrapper">
        @foreach ($trendingCourses as $trendingCourse)
            @php
                $averageRating = round($trendingCourse->average_rating);
            @endphp
            <div class="swiper-slide">
                <div class="card shadow">
                    <div class="card-img-top">
                        <img src="{{ asset('storage/' . $trendingCourse->thumbnail) }}" class="" alt="...">
                        <i class="{{ Auth::check() && Auth::user()->wishlist->contains($trendingCourse) ? 'fa-solid' : 'fa-regular' }} fa-bookmark wishlist-icon"
                            data-course-id="{{ $trendingCourse->id }}" style="cursor: pointer;">
                        </i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $trendingCourse->title }}</h5>
                        <div class="tutor-ratings d-flex align-items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $averageRating ? 'fa-solid' : 'fa-regular' }} fa-star"
                                    style="color: #eea015;"></i>
                            @endfor
                        </div>
                        <div class="tutor-meta-wrapper d-flex align-items-center gap-5">
                            <div class="tutor-data d-flex align-items-center gap-1">
                                <img src="{{ $trendingCourse->user->pfp ? asset('storage/' . $trendingCourse->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="">
                                <small>by</small><span>{{ $trendingCourse->user->first_name }}
                                    {{ $trendingCourse->user->last_name }}</span>
                            </div>
                        </div>
                        <p class="card-text ">{{ $trendingCourse->description }}</p>
                        <div class="post-details d-flex align-items-center">
                            <span class="date">Publish
                                :{{ $trendingCourse->created_at->diffForHumans() }}</span>
                        </div>
                        <a class="button-primary mt-3 d-block w-100 text-center" target="_blank"
                            href="{{ route('course.details', $trendingCourse->id) }}">More
                            Details...</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
