<div class="swiper" wire:ignore>
    <div class="arrows d-flex align-items-center justify-content-end gap-2">
        <div class="swiper-button-prev position-relative"></div>
        <div class="swiper-button-next position-relative"></div>
    </div>
    <div class="swiper-wrapper">
        @foreach ($newCourses as $newCourse)
            @php
                $averageRating = round($newCourse->average_rating);
            @endphp
            <div class="swiper-slide">
                <div class="card shadow">
                    <div class="card-img-top">
                        <img src="{{ asset('storage/' . $newCourse->thumbnail) }}" class="" alt="...">
                        <i class="{{ Auth::check() && Auth::user()->wishlist->contains($newCourse) ? 'fa-solid' : 'fa-regular' }} fa-bookmark wishlist-icon"
                            data-course-id="{{ $newCourse->id }}" style="cursor: pointer;">
                        </i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $newCourse->title }}</h5>
                        <div class="tutor-ratings d-flex align-items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $averageRating ? 'fa-solid' : 'fa-regular' }} fa-star"
                                    style="color: #eea015;"></i>
                            @endfor
                        </div>
                        <div class="tutor-meta-wrapper d-flex align-items-center gap-5">
                            <div class="tutor-data d-flex align-items-center gap-1">
                                <img src="{{ $newCourse->user->pfp ? asset('storage/' . $newCourse->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="">
                                <small>by</small><span>{{ $newCourse->user->first_name }}
                                    {{ $newCourse->user->last_name }}</span>
                            </div>
                        </div>
                        <p class="card-text ">{{ $newCourse->description }}</p>
                        <div class="post-details d-flex align-items-center">
                            <span class="date">Publish
                                :{{ $newCourse->created_at->diffForHumans() }}</span>
                        </div>
                        <a class="button-primary mt-3 d-block w-100 text-center" target="_blank"
                            href="{{ route('course.details', $newCourse->id) }}">More
                            Details...</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
{{-- @push('js')
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
@endpush --}}
