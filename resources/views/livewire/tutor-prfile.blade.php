<div class="tutor-profile">
    <section class="banner">
        <div class="container">
            <div class="banner-image"
                style="background-image: url('{{ asset('storage/' . $tutor->user->cover_photo) }}'); background-size: cover">
                <div class="row align-items-center z-1 ">
                    <div class="col-lg-3">
                        <div class="profile">
                            <img src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                alt="">
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="profile-details">
                            <div class="details-profile">
                                <h3>{{ $tutor_name }}</h3>
                                <span>{{ $tutorCourses->count() }} courses</span>
                                <span>.</span>
                                <span>48 students</span>
                            </div>
                            <div class="rating-details d-flex">
                                <span>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                </span>
                                <p>4.80 <span>(10.00)</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="tutor-profile-content position-relative">
        <div class="container">
            <div class="inner">
                <div class="row">
                    <div class="col-md-12 col-lg-9 ms-auto">
                        <div
                            class="profile-details-mobile d-block d-lg-none d-flex align-items-center justify-content-center flex-column">
                            <div class="profile">
                                <img src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="">
                            </div>
                            <div class="profile-details d-flex flex-column align-items-center justify-content-center">
                                <div class="details-profile">
                                    <h3 class="text-center">{{ $tutor_name }}</h3>
                                    <span>{{ $tutorCourses->count() }} courses</span>
                                    <span>.</span>
                                    <span>48 students</span>
                                </div>
                                <div class="rating-details d-flex">
                                    <span>
                                        <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                        <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                        <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                        <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                        <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                    </span>
                                    <p><span>4.80 </span>(10.00)</p>
                                </div>
                            </div>
                        </div>
                        <div class="tutor-user-profile-content tutor-d-block mt-0 mt-lg-5 px-3">
                            <h3>Biography</h3>
                            <p>{{ $tutor_bio }}</p>

                            <h3>Courses</h3>
                            @if ($tutorCourses->count() < 0)
                                <div class="tutor-pagination-wrapper-replaceable">
                                    <div class="tutor-course-list tutor-grid tutor-grid-3">
                                        <p>No course yet.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row gy-4">
                            <!-- Repeat card structure for course cards -->
                            @foreach ($tutorCourses as $tutorCourse)
                                <div class="col-md-6  col-xl-4">
                                    <div class="card shadow">
                                        <div class="card-img-top">
                                            <img src="{{ asset('storage/' . $tutorCourse->thumbnail) }}"
                                                class="card-img-top" alt="...">
                                            <i class="fa-regular fa-bookmark"></i>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $tutorCourse->title }}</h5>
                                            <div class="tutor-ratings d-flex align-items-center gap-1">
                                                <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                                <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                                <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                                <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                                <i class="fa-regular fa-star" style="color: #eea015;"></i>
                                            </div>
                                            <div class="tutor-meta-wrapper d-flex align-items-center gap-5">
                                                <div class="tutor-data d-flex align-items-center gap-1">
                                                    <img src="{{ $tutorCourse->user->pfp ? asset('storage/' . $tutorCourse->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                                        alt="">
                                                    <small>by</small><span>{{ $tutorCourse->user->first_name }}
                                                        {{ $tutorCourse->user->last_name }}</span>
                                                </div>
                                                {{-- <div class="tutor-meta-icon d-flex align-items-center gap-2">
                                                    <div class="meta-icon d-flex align-items-center">
                                                        <i class="fa-regular fa-user" style="color: #757c8e;"></i>
                                                        <span>11</span>
                                                    </div>
                                                    <div class="meta-icon d-flex align-items-center">
                                                        <i class="fa-regular fa-clock" style="color: #757c8e;"></i>
                                                        <span>2h</span>
                                                    </div>
                                                </div> --}}
                                            </div>
                                            <p class="card-text">
                                                {{ $tutorCourse->description }}
                                            </p>
                                            <div class="post-details text-center">
                                                <span class="date">Publish:
                                                    {{ $tutorCourse->created_at->diffForHumans() }}</span>
                                            </div>
                                            <a class="button-primary mt-3 d-block w-100 text-center"
                                                href="{{ route('course.details', $tutorCourse->id) }}">More
                                                Details...</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            {{ $tutorCourses->links() }}
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
