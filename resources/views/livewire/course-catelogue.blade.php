@php
    $user = Auth::user();
@endphp
<div class="course-catalogue">
    @push('title')
        Course Catalog
    @endpush
    <section class="course-catelogue-main">
        <div class="container d-flex flex-column justify-content-center align-items-center">
            <div class="content-wrapper">
            <h3>PROGRAMS WE OFFER</h3>
            <h1 class="my-5">ALL COURSES</h1>
        </div>
        </div>
    </section>
    <section class="course-catelogue-list">
        <div class="container mt-5">
            <div x-data="{ tab: 'mostPopular' }" class="container mt-5">

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ 'active': tab === 'mostPopular' }"
                            @click="tab = 'mostPopular'">Most Popular</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ 'active': tab === 'new' }" @click="tab = 'new'">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ 'active': tab === 'trending' }"
                            @click="tab = 'trending'">Trending</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-4">
                    <!-- Most Popular Tab -->
                    <div x-show="tab === 'mostPopular'">
                        <livewire:courses-cards.most-popular-cards />
                    </div>
                    <!-- New Tab -->
                    <div x-show="tab === 'new'">
                        <livewire:courses-cards.new-cards />
                    </div>
                    <!-- Trending Tab -->
                    <div x-show="tab === 'trending'">
                        <livewire:courses-cards.trending-cards />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="course-catelogue pt-3">
        <div class="container">
            <h2 class="my-5">Category</h2>
            <div class="filters d-flex justify-content-between align-items-center mb-4">
                <!-- Search Input -->
                <div class="input-group w-50 position-relative filter-search">
                    <div class="input-group-prepend">
                        <span class="input-group-text position-absolute" id="search-icon">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input type="text" placeholder="Search courses by title..." wire:model.live="search"
                        class="form-control mb-3" />
                </div>
                <button class="offcanvas-btn d-block d-flex d-xl-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="fa-solid fa-sliders"></i>
                </button>
            </div>

            <!-- Offcanvas component -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="filter">
                        <!-- Search Input -->
                        <div class="input-group position-relative filter-search">
                            <div class="input-group-prepend">
                                <span class="input-group-text position-absolute" id="search-icon">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                                aria-describedby="search-icon">
                        </div>
                        <ul class="my-4">
                            <h4>Category</h4>
                            <li>
                                <input type="checkbox" id="ethics">
                                <label for="ethics">Ethics</label>
                            </li>
                            <li>
                                <input type="checkbox" id="mental-health">
                                <label for="mental-health">Mental Health</label>
                            </li>
                            <li>
                                <input type="checkbox" id="substance-use">
                                <label for="substance-use">Substance Use Disorder</label>
                            </li>
                        </ul>
                        <button class="btn filter-btn" wire:click="clearFilters">
                            <i class="fa-solid fa-xmark"></i> Clear All Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 d-none d-xl-block">
                    <div class="filter d-none d-xl-block">
                        <ul class="my-4">
                            <h4>Category</h4>
                            @foreach ($categories as $category)
                                <li>
                                    <input type="checkbox" id="category-{{ $category->id }}" value="{{ $category->id }}"
                                        wire:change="selectedCategorieschanged({{ $category->id }})">
                                    <label for="category-{{ $category->id }}">{{ $category->category_name }}</label>
                                </li>
                            @endforeach
                            {{-- @dump($selectedCategories) --}}
                        </ul>
                        <button class="btn filter-btn" wire:click="clearFilters">
                            <i class="fa-solid fa-xmark"></i> Clear All Filters
                        </button>
                    </div>

                </div>
                <div class="col-lg-12 col-xl-9">
                    <div class="row gy-4">
                        @foreach ($courses as $item)
                            @php
                                $averageRating = round($item->average_rating);
                            @endphp
                            <div class="col-sm-6 col-lg-4 ">
                                <div class="card shadow">
                                    <div class="card-img-top">
                                        <img src="{{ asset('storage/' . $item->thumbnail) }}" class=""
                                            alt="...">
                                        @if (Auth::check())
                                            <i class="{{ Auth::user()->wishlist->contains($item) ? 'fa-solid' : 'fa-regular' }} fa-bookmark wishlist-icon"
                                                data-course-id="{{ $item->id }}" style="cursor: pointer;">
                                            </i>
                                        @else
                                            <i class="fa-regular fa-bookmark" style="cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#AuthCheckModal">
                                            </i>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $item->title }}</h5>
                                        <div class="tutor-ratings d-flex align-items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $averageRating ? 'fa-solid' : 'fa-regular' }} fa-star"
                                                    style="color: #eea015;"></i>
                                            @endfor
                                        </div>
                                        <div class="tutor-meta-wrapper d-flex align-items-center gap-5">
                                            <div class="tutor-data d-flex align-items-center gap-1">
                                                <img src="{{ $item->user->pfp ? asset('storage/' . $item->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                                    alt="">
                                                <small>by</small><span>{{ $item->user->first_name }}
                                                    {{ $item->user->last_name }}</span>
                                            </div>
                                            <div class="tutor-meta-icon d-flex align-items-center gap-2">

                                            </div>
                                        </div>
                                        <p class="card-text">{{ $item->description }}
                                        </p>
                                        <div class="post-details">
                                            <div>
                                                <span class="date">Published:
                                                    {{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <a class="button-primary mt-3 d-block w-100 text-center" href="#">More
                                            Details...</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="instructor-slider">
        <div class="container">
            <div class="inner">
                <h2>POPULAR INSTRUCTORS</h2>
                <livewire:courses-cards.instructor-cards />
            </div>
        </div>
    </div>

    <style>
        .course-catelogue-main {
            background-image: url('{{ asset('assets/images/course.webp') }}');
        }
    </style>

</div>
@script
    <script type="text/javascript">
        const swiper = new Swiper('.swiper', {
            loop: true,
            grabCursor: true,
            // spaceBetween: 30, // Adjust the spacing between slides
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 0,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 0,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 0,
                },
                1440: {
                    slidesPerView: 4,
                    spaceBetween: 0,
                },
            }
        });

        document.addEventListener('clearCategoryFilters', () => {
            document.querySelectorAll('.filter input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    </script>
@endscript
