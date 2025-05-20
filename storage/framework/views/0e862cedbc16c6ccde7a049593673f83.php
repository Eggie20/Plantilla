<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Activity Log</h4>
                        <div class="d-flex gap-2">
                            <div class="form-group">
                                <select class="form-select" id="roleFilter">
                                    <option value="">All Roles</option>
                                    <?php
                                        $roles = \App\Models\PlantillaUser::distinct()->pluck('role')->toArray();
                                        sort($roles);
                                    ?>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role); ?>"><?php echo e($role); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-select" id="dateFilter">
                                    <option value="">All Dates</option>
                                    <optgroup label="Filter by Day">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="last7days">Last 7 Days</option>
                                        <option value="last30days">Last 30 Days</option>
                                    </optgroup>
                                    <optgroup label="Filter by Week">
                                        <option value="thisweek">This Week</option>
                                        <option value="lastweek">Last Week</option>
                                        <option value="last4weeks">Last 4 Weeks</option>
                                    </optgroup>
                                    <optgroup label="Filter by Year">
                                        <option value="thisyear">This Year</option>
                                        <option value="lastyear">Last Year</option>
                                        <option value="last2years">Last 2 Years</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="search-box">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search activities...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="activityTable">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td data-search="<?php echo e(\App\Models\PlantillaUser::find($activity->causer_id)?->username ?? 'N/A'); ?>">
                                        <?php echo e(\App\Models\PlantillaUser::find($activity->causer_id)?->username ?? 'N/A'); ?>

                                    </td>
                                    <td data-search="<?php echo e(\App\Models\PlantillaUser::find($activity->causer_id)?->role ?? 'N/A'); ?>">
                                        <?php echo e(\App\Models\PlantillaUser::find($activity->causer_id)?->role ?? 'N/A'); ?>

                                    </td>
                                    <td data-search="<?php echo e($activity->description); ?>">
                                        <?php echo e($activity->description); ?>

                                    </td>
                                    <td data-search="<?php echo e($activity->formatted_time); ?>">
                                        <?php echo e($activity->formatted_time); ?>

                                    </td>
                                    <td data-search="<?php echo e($activity->formatted_date); ?>">
                                        <?php echo e($activity->formatted_date); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php echo e($activities->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const dateFilter = document.getElementById('dateFilter');
    const activityTable = document.getElementById('activityTable');
    const rows = activityTable.getElementsByTagName('tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;
        const selectedDate = dateFilter.value;
        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        for (let row of rows) {
            if (row.getElementsByTagName('td').length > 0) {
                let showRow = true;
                const cells = row.getElementsByTagName('td');
                
                // Search filter
                if (searchTerm) {
                    let found = false;
                    for (let cell of cells) {
                        const cellText = cell.getAttribute('data-search').toLowerCase();
                        if (cellText.includes(searchTerm)) {
                            found = true;
                            break;
                        }
                    }
                    showRow = showRow && found;
                }

                // Role filter
                if (selectedRole && selectedRole !== "") {
                    const roleCell = cells[1]; // Role is in the second column
                    showRow = showRow && roleCell.getAttribute('data-search') === selectedRole;
                }

                // Date filter
                if (selectedDate && selectedDate !== "") {
                    const dateCell = cells[4]; // Date is in the fifth column
                    const rowDate = new Date(dateCell.getAttribute('data-search'));
                    
                    switch(selectedDate) {
                        case 'today':
                            showRow = showRow && rowDate.toDateString() === today.toDateString();
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(today.getDate() - 1);
                            showRow = showRow && rowDate.toDateString() === yesterday.toDateString();
                            break;
                        case 'last7days':
                            const last7days = new Date(today);
                            last7days.setDate(today.getDate() - 7);
                            showRow = showRow && rowDate >= last7days;
                            break;
                        case 'last30days':
                            const last30days = new Date(today);
                            last30days.setDate(today.getDate() - 30);
                            showRow = showRow && rowDate >= last30days;
                            break;
                        case 'thisweek':
                            showRow = showRow && rowDate >= weekStart;
                            break;
                        case 'lastweek':
                            const lastWeekStart = new Date(weekStart);
                            lastWeekStart.setDate(weekStart.getDate() - 7);
                            const lastWeekEnd = new Date(weekStart);
                            lastWeekEnd.setDate(weekStart.getDate() - 1);
                            showRow = showRow && rowDate >= lastWeekStart && rowDate <= lastWeekEnd;
                            break;
                        case 'last4weeks':
                            const last4weeks = new Date(weekStart);
                            last4weeks.setDate(weekStart.getDate() - 28);
                            showRow = showRow && rowDate >= last4weeks;
                            break;
                        case 'thisyear':
                            const yearStart = new Date(today.getFullYear(), 0, 1);
                            showRow = showRow && rowDate >= yearStart;
                            break;
                        case 'lastyear':
                            const lastYearStart = new Date(today.getFullYear() - 1, 0, 1);
                            const lastYearEnd = new Date(today.getFullYear() - 1, 11, 31);
                            showRow = showRow && rowDate >= lastYearStart && rowDate <= lastYearEnd;
                            break;
                        case 'last2years':
                            const last2YearsStart = new Date(today.getFullYear() - 2, 0, 1);
                            showRow = showRow && rowDate >= last2YearsStart;
                            break;
                    }
                }

                row.style.display = showRow ? '' : 'none';
            }
        }
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('Plantilla.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\plantilla versions\plantilla v1.9 - Copy\resources\views/Plantilla/Pages/activity-log.blade.php ENDPATH**/ ?>