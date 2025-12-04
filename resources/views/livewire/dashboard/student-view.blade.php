<section class="student-view py-5 px-4">
    @push('title')
        Student View
    @endpush
    <a href="#" class="mb-4 d-block text-black"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
    <div class="user-profile mb-5">
        <div class="cover-picture">
            <div class="profile-wrapper">
                <div class="profile-image">
                    <img src="" alt="">
                </div>
                <div class="profile-content">
                    <strong>Bilal Khalid</strong>
                    <ul>
                        <li style="list-style-type: none"><a href="#" class="ms-2">Email:
                                bilal@freedomhousetraining.org</a></li>
                        <li><a href="#" class="ms-2">Registration Date: 2023-12-19</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <h4 class="mb-4">Course Overview</h4>
        <div class="table-responsive">
            <div class="card-table">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Course</th>
                            <th scope="col">Progress</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    2024-05-29
                                </p>
                            </td>
                            <td>
                                <p>Documentation for Authorization </p>
                                <ul>
                                    <li>Lesson: <span>0/0</span></li>
                                    <li>Assignment: <span>0/0</span></li>
                                    <li>Quiz: <span>0/0</span></li>
                                </ul>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <div class="progres-bar d-flex gap-1">
                                        <div class="progress">
                                            <div class="progress-bar w-75" role="progressbar" aria-valuenow="75"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span>0%</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="button-secondary sm-btn">View Progress</button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>
