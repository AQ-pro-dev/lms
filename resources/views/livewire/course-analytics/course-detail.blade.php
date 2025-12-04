<div class="course-details px-4" x-data="{ tab: 'all' }">

    <div class="my-4">
        <a href="" class="back-link my-3"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <div class="course-header my-3">
        <h1 class="my-3">Clinical Documentation</h1>
        <p>
            Published Date: <span>2024-05-30</span> &nbsp; | &nbsp;
            Last Update: <span>2024-06-25</span>
        </p>
    </div>

    <div class="enrollment-section mt-4">

        <div class="d-flex align-items-center justify-content-between tabs px-1">
            <h2>Enrolled Students</h2>
            <ul class="nav nav-tabs border-0 mt-3">
                <li class="nav-item">
                    <a @click="tab = 'all'" :class="{ 'active': tab === 'all' }" href="#" class="nav-link">All
                        (11)</a>
                </li>
                <li class="nav-item">
                    <a @click="tab = 'approved'" :class="{ 'active': tab === 'approved' }" href="#"
                        class="nav-link">Approved (9)</a>
                </li>
                <li class="nav-item">
                    <a @click="tab = 'cancelled'" :class="{ 'active': tab === 'cancelled' }" href="#"
                        class="nav-link">Cancelled (2)</a>
                </li>
            </ul>
        </div>

        <div class="bulk-action my-5">
            <select class="form-select">
                <option>Bulk Action</option>
                <option>Approve</option>
                <option>Cancel</option>
                <option>Delete</option>
            </select>
            <button class="button-secondary">Apply</button>
        </div>
        <div class="table-responsive mt-3">
            <template x-if="tab === 'all'">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Completion Status</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2024-10-23 11:25 am</td>
                            <td>Clinical Documentation <a href="#"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                            <td><span class="badge badge-incomplete">InComplete</span></td>
                            <td>tyra.h@freedomhouserecovery.org<br><small>tyra.h@freedomhouserecovery.org</small></td>
                            <td><span class="badge badge-approved">Approved</span></td>
                        </tr>
                        <!-- Repeat for other students -->
                    </tbody>
                </table>
            </template>

            <template x-if="tab === 'approved'">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Completion Status</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2024-10-20 10:00 am</td>
                            <td>Clinical Documentation <a href="#"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                            <td><span class="badge badge-complete">Complete</span></td>
                            <td>john.doe@example.com<br><small>john.doe@example.com</small></td>
                            <td><span class="badge badge-approved">Approved</span></td>
                        </tr>
                        <!-- Repeat for approved students -->
                    </tbody>
                </table>
            </template>

            <template x-if="tab === 'cancelled'">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Date</th>
                            <th>Course</th>
                            <th>Completion Status</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>2024-10-15 8:45 am</td>
                            <td>Clinical Documentation <a href="#"><i class="fas fa-external-link-alt"></i></a>
                            </td>
                            <td><span class="badge badge-cancelled">Cancelled</span></td>
                            <td>jane.doe@example.com<br><small>jane.doe@example.com</small></td>
                            <td><span class="badge badge-cancelled">Cancelled</span></td>
                        </tr>
                        <!-- Repeat for cancelled students -->
                    </tbody>
                </table>
            </template>
        </div>
    </div>
</div>
