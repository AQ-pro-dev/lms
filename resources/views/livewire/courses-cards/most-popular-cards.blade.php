<div class="swiper" wire:ignore>
    <div class="arrows d-flex align-items-center justify-content-end gap-2">
        <div class="swiper-button-prev position-relative"></div>
        <div class="swiper-button-next position-relative"></div>
    </div>
    <div class="swiper-wrapper">
        @foreach ($mostPopularCourses as $mostPopularCourse)
            @php
                $averageRating = round($mostPopularCourse->average_rating);
            @endphp
            <div class="swiper-slide">
                <div class="card shadow">
                    <div class="card-img-top">
                        <img src="{{ asset('storage/' . $mostPopularCourse->thumbnail) }}"
                            class="
                        " alt="...">

                        <!-- Wishlist Icon with Dynamic Class Toggle -->
                        @if (Auth::check())
                            <i class="{{ Auth::user()->wishlist->contains($mostPopularCourse) ? 'fa-solid' : 'fa-regular' }} fa-bookmark wishlist-icon"
                                data-course-id="{{ $mostPopularCourse->id }}" style="cursor: pointer;">
                            </i>
                        @else
                            <i class="fa-regular fa-bookmark" style="cursor: pointer;" data-bs-toggle="modal"
                                data-bs-target="#AuthCheckModal">
                            </i>
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $mostPopularCourse->title }}</h5>
                        <div class="tutor-ratings d-flex align-items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $averageRating ? 'fa-solid' : 'fa-regular' }} fa-star"
                                    style="color: #eea015;"></i>
                            @endfor
                        </div>
                        <div class="tutor-meta-wrapper d-flex align-items-center gap-5">
                            <div class="tutor-data d-flex align-items-center gap-1">
                                <img src="{{ $mostPopularCourse->user->pfp ? asset('storage/' . $mostPopularCourse->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="">
                                <small>by</small><span>{{ $mostPopularCourse->user->first_name }}
                                    {{ $mostPopularCourse->user->last_name }}</span>
                            </div>
                        </div>
                        <p class="card-text ">{{ $mostPopularCourse->description }}</p>
                        <div class="post-details d-flex align-items-center">
                            <span class="date">Publish: {{ $mostPopularCourse->created_at->diffForHumans() }}</span>
                        </div>
                        {{-- @if (Auth::check())
                            <a class="button-primary mt-3 d-block w-100 text-center" target="_blank"
                                href="{{ route('course.details', $mostPopularCourse->id) }}">More Details...</a>
                        @else
                            <a class="button-primary mt-3 d-block w-100 text-center"data-bs-toggle="modal"
                                data-bs-target="#AuthCheckModal" target="_blank" href="">More Details...</a>
                        @endif --}}
                        @if ($mostPopularCourse->course_type == 'recorded')
                            <a class="button-primary mt-3 d-block w-100 text-center" target="_blank"
                                href="{{ route('onsite.course.details', $mostPopularCourse->id) }}">More Details...</a>
                        @else
                            <a class="button-primary mt-3 d-block w-100 text-center" target="_blank"
                                href="{{ route('course.details', $mostPopularCourse->id) }}">More Details...</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            // Handle wishlist icon click for all icons dynamically
            $('.wishlist-icon').on('click', function() {
                var courseId = $(this).data('course-id');
                var icon = $(this);

                // Call Livewire method without re-rendering the entire component
                @this.call('toggleWishlist', courseId);

                // Toggle the icon class after Livewire call
                if (icon.hasClass('fa-regular')) {
                    icon.removeClass('fa-regular').addClass('fa-solid');
                } else {
                    icon.removeClass('fa-solid').addClass('fa-regular');
                }
            });
        });
    </script>
@endpush
