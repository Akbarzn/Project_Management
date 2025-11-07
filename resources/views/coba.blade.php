public function index()
    {
        $tasks = Task::where('karyawan_id', Auth::user()->karyawan->id)
                     ->with('project')
                     ->latest()
                     ->get();

        return view('karyawan.tasks.index', compact('tasks'));


    {{-- JS Live Search --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const taskTable = document.getElementById('taskTable');

    function fetchData() {
        const search = searchInput.value;
        const status = statusFilter.value;

        fetch(`{{ route('manager.tasks.index') }}?search=${search}&status=${status}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            taskTable.innerHTML = html;
        });
    }

    searchInput.addEventListener('keyup', fetchData);
    statusFilter.addEventListener('change', fetchData);
});
</script>