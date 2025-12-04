<div class="wishlist py-5 px-4">
    @push('title')
        WishList
    @endpush
    <h2 class="mb-4">WishList</h2>
    <div class="wishlist-cards d-flex flex-wrap gap-4">
        @forelse ($wishlists as $wishlist)
            <div class="card shadow">
                <div class="card-img-top">
                    <img src="{{ asset('storage/' . $wishlist->course->thumbnail) }}" alt="Featured Card">
                    <i class="{{ $wishlist->course->id && Auth::check() && Auth::user()->wishlist->contains($wishlist->course->id)
                        ? 'fa-solid'
                        : 'fa-regular' }} fa-bookmark wishlist-icon"
                        style="cursor: pointer;" wire:click="toggleWishlist({{ $wishlist->course->id }})">
                    </i>
                </div>
                <div class="card-body">
                    <div class="tutor-ratings d-flex align-items-center gap-1">
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <h5 class="card-title">{{ $wishlist->course->title }}</h5>
                    <div class="tutor-meta-wrapper d-flex align-items-center gap-3">
                        <div class="tutor-data d-flex align-items-center gap-1">
                            <img src="{{ $wishlist->course->user->pfp ? $wishlist->course->user->pfp : asset('assets/images/dummy-profile-photo.webp') }}"
                                alt="Creator">
                            <small>by</small>
                            <span>{{ $wishlist->course->user->first_name ?? 'N/A' }}
                                {{ $wishlist->course->user->last_name ?? '' }}</span>
                        </div>
                    </div>

                    @if ($wishlist->course->course_type == 'classtype')
                        <a href="{{ route('course.details', $wishlist->course->id) }}"
                            class="button-primary text-center">Start Learning</a>
                    @elseif ($wishlist->course->course_type == 'recorded')
                        <a href="{{ route('onsite.course.details', $wishlist->course->id) }}"
                            class="button-primary text-center">Start Learning</a>
                    @endif

                </div>
            </div>
        @empty
            <p>No courses in your wishlist yet.</p>
        @endforelse
    </div>
</div>
