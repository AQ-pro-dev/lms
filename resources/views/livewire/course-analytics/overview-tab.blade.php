<div class="overview px-5">
    <section class="overview-boxes d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <div class="overview-box d-flex align-items-center justify-content-center flex-column mb-3">
            <div class="overview-box-icon">
                <i class="fa-brands fa-google-scholar"></i>
            </div>
            <div class="number">
                {{ $courseCount }}
            </div>
            <span>Total courses</span>
        </div>
        <div class="overview-box d-flex align-items-center justify-content-center flex-column mb-3">
            <div class="overview-box-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="number">
                {{ $studentCount }}
            </div>
            <span>Total Student</span>
        </div>
        <div class="overview-box d-flex align-items-center justify-content-center flex-column mb-3">
            <div class="overview-box-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="number">
                {{ $reviewCount }}
            </div>
            <span>Total Reviews</span>
        </div>
    </section>
    <section class="most-popular-courses-table">
        <h4 class="my-4">Most Popular Courses</h4>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Course Name</th>
                            <th scope="col">Enrolled</th>
                            <th scope="col">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($highestRatedCourse as $highCourse)
                            <tr>
                                <td>
                                    <a href="{{ route('course.details', $highCourse->id) }}">
                                        <!-- Replace with actual route -->
                                        {{ $highCourse->title }}
                                    </a>
                                </td>
                                <td>
                                    {{ $highCourse->bookings_count ?? 0 }} <!-- Dynamic enrollment count -->
                                </td>
                                <td>
                                    <span>
                                        @php
                                            $averageRating = $highCourse->reviews_avg_rating ?? 0; // Default to 0 if no reviews
                                        @endphp

                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($averageRating))
                                                <i class="fa fa-star" style="color: #eea015;"></i> <!-- Filled star -->
                                            @elseif ($i - $averageRating < 1 && $averageRating - floor($averageRating) >= 0.5)
                                                <i class="fa fa-star-half-o" style="color: #eea015;"></i>
                                                <!-- Half star -->
                                            @else
                                                <i class="fa fa-star-o" style="color: #ccc;"></i> <!-- Empty star -->
                                            @endif
                                        @endfor
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    <i class="fa fa-info-circle"></i> No courses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


            </div>
        </div>
    </section>
    <section class="Review-table">
        <h4 class="my-4">Recent Reviews</h4>
        <div class="card-table">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">Student</th>
                            <th scope="col">Date</th>
                            <th scope="col">Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentReviews as $review)
                            <tr>
                                <td class="d-flex align-items-center">
                                    <div class="review-img me-2">
                                        <img src="{{ $review->user->profile_picture ?? asset('assets/images/dummy-profile-photo.webp') }}"
                                            alt="{{ $review->user->name }}" class="rounded-circle"
                                            style="width: 40px; height: 40px;">
                                    </div>
                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                </td>
                                <td>{{ $review->created_at->format('Y-m-d h:i A') }}</td>
                                <td>
                                    <div class="star-rating" style="color: #eea015;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fa fa-star"></i> <!-- Filled star -->
                                            @else
                                                <i class="fa fa-star-o"></i> <!-- Empty star -->
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="mb-1 course-feedback">{{ $review->review ?? 'No feedback provided.' }}
                                    </p>
                                    <p class="mb-0 course-name">Course: {{ $review->course->title }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No recent reviews found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section wire:ignore.self id="student-enrollment-chart">
        <h4 class="my-4">Student Enrollment by Course</h4>
        <div id="chart"></div>
    </section>
</div>
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var enrollmentData = @this.enrollmentData; // Get the data from Livewire component

            var courses = @json($highestRatedCourse->pluck('title'));
            var enrollments = Object.values(enrollmentData);
            console.log(enrollments);


            var options = {
                series: [{
                    name: 'Enrollments',
                    data: enrollments
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        endingShape: 'rounded',
                        columnWidth: '55%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: courses,
                },
                title: {
                    text: 'Student Enrollment by Course',
                    align: 'center'
                }
            };
            setTimeout(() => {
                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            }, 3000);
            setTimeout(() => {
                document.addEventListener('renderChart', function() {
                    console.log('hihi');
                    chart.render();
                })
            }, 3000);
        });
    </script>
@endpush
