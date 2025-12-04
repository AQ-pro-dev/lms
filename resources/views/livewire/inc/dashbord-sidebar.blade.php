<div class="dashboard-sidebar" id="dashboard-sidebar">
    <div class="sidebar">
        <div class="nav d-flex flex-column">
            <ul>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('dashboard') }}" wire:navigate>
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.profile' ? 'active' : '' }}"
                        href="{{ route('dashboard.profile') }}" wire:navigate>
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.enrolledCourses' ? 'active' : '' }}"
                        href="{{ route('dashboard.enrolledCourses') }}" wire:navigate>
                        <i class="fas fa-book"></i> Enrolled Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.wishlist' ? 'active' : '' }}"
                        href="{{ route('dashboard.wishlist') }}" wire:navigate>
                        <i class="fas fa-heart"></i> Wishlist
                    </a>
                </li>
                @if (Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.help' ? 'active' : '' }}"
                            href="{{ route('dashboard.help') }}" wire:navigate>
                            <i class="fas fa-question-circle"></i> Help Request
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.enrollments' ? 'active' : '' }}"
                        href="{{ route('dashboard.enrollments') }}" wire:navigate>
                        <i class="fas fa-list"></i> View Enrollments
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.scehedule-classes' ? 'active' : '' }}"
                        href="{{ route('dashboard.scehedule-classes') }}" wire:navigate>
                        <i class="fa-solid fa-list"></i> Schedule Classes
                    </a>
                </li> --}}
                @if (Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
                    <li class="tutor-dashboard-menu-divider-header">
                        Instructor
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.mycourses' ? 'active' : '' }}"
                            href="{{ route('dashboard.mycourses') }}" wire:navigate>
                            <i class="fa-solid fa-rocket"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.announcements' ? 'active' : '' }}"
                            href="{{ route('dashboard.announcements') }}" wire:navigate>
                            <i class="fa-solid fa-bullhorn"></i> Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.lectures' ? 'active' : '' }}"
                            href="{{ route('dashboard.lectures') }}" wire:navigate>
                            <i class="fa-solid fa-video"></i> Upload Lectures
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.analytics' ? 'active' : '' }}"
                            href="{{ route('dashboard.analytics') }}">
                            <i class="fa-solid fa-book"></i> Course Analytics
                        </a>
                    </li>
                @endif
                @if (Auth::user()->role_id == 1)
                    <li class="tutor-dashboard-menu-divider-header">
                        Admin
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard/users*') ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#usersSubMenu" role="button" aria-expanded="false"
                            aria-controls="usersSubMenu">
                            <i class="fas fa-users"></i> Users
                            <i class="fas fa-caret-down float-end"></i>
                        </a>
                        <div class="collapse {{ Request::is('dashboard/users*') ? 'show' : '' }}" id="usersSubMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.users.students' ? 'active' : '' }}"
                                        href="{{ route('dashboard.students') }}" wire:navigate>
                                        <i class="fas fa-user-graduate"></i> Students
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.users.instructors' ? 'active' : '' }}"
                                        href="" wire:navigate>
                                        <i class="fas fa-chalkboard-teacher"></i> Instructors
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.users.admins' ? 'active' : '' }}"
                                        href="" wire:navigate>
                                        <i class="fa-solid fa-user-tie"></i> Admins
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.tutor.request' ? 'active' : '' }}"
                            href="{{ route('dashboard.tutor.request') }}" wire:navigate>
                            <i class="fa-solid fa-person-circle-question"></i> Tutor Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.coursesRequest' ? 'active' : '' }}"
                            href="{{ route('dashboard.coursesRequest') }}" wire:navigate>
                            <i class="fa-solid fa-clipboard-check"></i> Courses Request
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.help.admin' ? 'active' : '' }}"
                            href="{{ route('dashboard.help.admin') }}" wire:navigate>
                            <i class="fa-regular fa-circle-question"></i>Help requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.categories' ? 'active' : '' }}"
                            href="{{ route('dashboard.categories') }}" wire:navigate>
                            <i class="fa-solid fa-table-list"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.tags' ? 'active' : '' }}"
                            href="{{ route('dashboard.tags') }}" wire:navigate>
                            <i class="fa-solid fa-tag"></i> Tags
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'dashboard.emails' ? 'active' : '' }}"
                            href="{{ route('dashboard.emails') }}" wire:navigate>
                            <i class="fas fa-newspaper"></i> Newsletter
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dashboard.settings' ? 'active' : '' }}"
                        href="{{ route('dashboard.settings') }}" wire:navigate>
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" wire:click="logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
