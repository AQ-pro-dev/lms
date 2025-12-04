<div class="dashboard-footer-mobile">

    <div class="tutor-dashboard-footer-mobile">
        <div class="tutor-container px-3">
            <div class="row d-flex align-items-center text-center mx-3">
                <a class="col-4 d-flex flex-column text-secondary" href="" >
                    <i class="fas fa-tachometer-alt"></i>
                    <span>My Courses</span>
                </a>
                <a class="col-4 d-flex flex-column text-secondary" href="">
                    <i class="fa-solid fa-person-circle-question"></i>
                    <span>Q&amp;A</span>
                </a>
                <a class="col-4 d-flex flex-column text-secondary" id="hamburger-icon-footer">
                    <i class="fa-solid fa-bars" ></i>
                    <span>Menu</span>
                </a>
            </div>
        </div>
    </div>
</div>
@script
@push('js')
<script>
$(document).ready(function() {
    $('#hamburger-icon-footer').click(function() {
        $('#dashboard-sidebar').toggleClass('visible'); // Toggle the "visible" class
    });
})
</script>
@endpush
@endscript
