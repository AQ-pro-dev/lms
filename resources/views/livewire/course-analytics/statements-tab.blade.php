<div class="statements-tab px-5">
    <div wire:ignore class="filters d-flex flex-column flex-md-row gap-3 align-items-start mb-4">
        <div class="flex-grow-1">
            <label for="courses" class="form-label">Courses</label>
            <select class="form-select w-100" id="courses" wire:model="selectedCourse" wire:change="fetchPayments">
                <option value="">All</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-grow-1">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="calendar-input-statements-tab" placeholder="Y-M-d"
                wire:model="selectedDate" wire:change="fetchPayments" />
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-borderless statements-table">
            <thead>
                <tr>
                    <th>Statement Info</th>
                    <th>My Earnings</th>
                    <th>Admin Gets</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    {{-- @dd($payment->booking->course->title) --}}
                    <tr>
                        <td>
                            <div class="statement-info">
                                <span class="text-muted">{{ $payment->booking->created_at->format('Y-m-d') }} ·
                                    {{ $payment->booking->course->title }}</span><br />
                                <h4>{{ $payment->booking->course->title }}</h4><br />
                                <a href="#" class="text-secondary">Order ID:
                                    #{{ $payment->booking->order_id }}</a>
                            </div>
                        </td>
                        <td>
                            <div class="earnings">
                                <strong>${{ $payment->amount }}</strong><br />
                                <small class="text-muted">{{ $payment->amount }} of
                                    ${{ $payment->booking->price }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="admin-gets">
                                <strong>${{ $payment->booking->price }}</strong><br />
                                <small class="text-muted">As per 100.00%</small>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@script
    <script>
        flatpickr("#calendar-input-statements-tab", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            defaultDate: new Date(),
        });
    </script>
@endscript
