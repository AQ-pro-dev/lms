<div class="course-analytics">
    @push('title')
        Course Analytics
    @endpush
    <div x-data="{ tab: 'Overview' }" class="container mt-5">

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Overview' }" @click="tab = 'Overview'"
                    aria-selected="tab === 'Overview'">
                    Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Courses' }" @click="tab = 'Courses'"
                    aria-selected="tab === 'Courses'">Courses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Earnings' }" @click="tab = 'Earnings'"
                    aria-selected="tab === 'Earnings'">Earnings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Statement' }" @click="tab = 'Statement'"
                    aria-selected="tab === 'Statement'">Statement</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Student' }" @click="tab = 'Student'"
                    aria-selected="tab === 'Student'">Student</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" role="tab" :class="{ 'active': tab === 'Export' }" @click="tab = 'Export'"
                    aria-selected="tab === 'Export'">Export</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <!-- Overview Tab -->
            <div x-show="tab === 'Overview'" x-transition>
                <livewire:course-analytics.overview-tab />
            </div>
            <!-- Courses Tab -->
            <div x-show="tab === 'Courses'" x-transition>
                <livewire:course-analytics.courses-tab />
            </div>
            <!-- Earnings Tab -->
            <div x-show="tab === 'Earnings'" x-transition>
                <livewire:course-analytics.earnings-tab />
            </div>
            <!-- Statement Tab -->
            <div x-show="tab === 'Statement'" x-transition>
                <livewire:course-analytics.statements-tab />
            </div>
            <!-- Student Tab -->
            <div x-show="tab === 'Student'" x-transition>
                <livewire:course-analytics.students-tab />
            </div>
            <!-- Export Tab -->
            <div x-show="tab === 'Export'" x-transition>
                <livewire:course-analytics.export-tab />
            </div>
        </div>
    </div>
</div>
