<div class="profile py-5 px-4">
    @push('title')
        My Profile
    @endpush
    <h2 class="mb-4">My Profile</h2>
    <ul class="profile-details">
        <li>
            <span class="label">Registration Date</span>
            <span class="value">{{ date('M d, Y', strtotime($user->created_at)) }}</span>
        </li>
        <li>
            <span class="label">First Name</span>
            <span class="value">{{ $user->first_name }}</span>
        </li>
        <li>
            <span class="label">Last Name</span>
            <span class="value">{{ $user->last_name }}</span>
        </li>
        <li>
            <span class="label">Username</span>
            <span class="value">{{ $user->username }}</span>
        </li>
        <li>
            <span class="label">Email</span>
            <span class="value">{{ $user->email }}</span>
        </li>
        <li>
            <span class="label">Phone Number</span>
            <span class="value">{{ $user->phone ?? '--' }}</span>
        </li>
        <li>
            <span class="label">Skill/Occupation</span>
            <span class="value">{{ $user->occupation ?? '--' }}</span>
        </li>
        <li>
            <span class="label">Biography</span>
            <span class="value">{{ $user->bio ?? '--' }}</span>
        </li>
    </ul>
</div>
