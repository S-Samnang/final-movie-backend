@extends(backpack_view('blank'))

@section('content')
<div class="container-fluid py-4">

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body text-center">
                    <h1>🎬 Welcome to <strong>FunWatch Admin</strong>!</h1>
                    <p>Manage Movies, Actors, and Users from this panel.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <!-- Users -->
        <div class="col-md-4">
            <a href="{{ backpack_url('user') }}" class="text-decoration-none">
                <div class="card text-white bg-gradient-success shadow-sm hover-zoom">
                    <div class="card-body text-center">
                        <i class="la la-users la-3x mb-2"></i>
                        <h5>Total Users</h5>
                        <h2>{{ \App\Models\User::count() }}</h2>
                    </div>
                </div>
            </a>
        </div>

        <!-- Movies -->
        <div class="col-md-4">
            <a href="{{ backpack_url('movie') }}" class="text-decoration-none">
                <div class="card text-white bg-gradient-info shadow-sm hover-zoom">
                    <div class="card-body text-center">
                        <i class="la la-film la-3x mb-2"></i>
                        <h5>Total Movies</h5>
                        <h2>{{ \App\Models\Movie::count() }}</h2>
                    </div>
                </div>
            </a>
        </div>

        <!-- Actors -->
        <div class="col-md-4">
            <a href="{{ backpack_url('actor') }}" class="text-decoration-none">
                <div class="card text-white bg-gradient-warning shadow-sm hover-zoom">
                    <div class="card-body text-center">
                        <i class="la la-user-friends la-3x mb-2"></i>
                        <h5>Total Actors</h5>
                        <h2>{{ \App\Models\Actor::count() }}</h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Chart + Latest Movies + Recent Users -->
    <div class="row">
        <!-- Chart -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header font-weight-bold">
                    📈 User Registrations
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Movies -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header font-weight-bold">
                    🎥 Latest Movies
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach(\App\Models\Movie::orderBy('created_at', 'desc')->take(5)->get() as $movie)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ \Illuminate\Support\Str::limit($movie->title, 30) }}
                                <small class="text-muted">{{ $movie->created_at->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header font-weight-bold">
                    👥 Recent Users
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Small custom CSS --}}
<style>
    .hover-zoom:hover {
        transform: scale(1.05);
        transition: all 0.3s ease;
    }
    .text-decoration-none:hover {
    text-decoration: none !important;
    }
</style>
@endsection

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('userGrowthChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_reverse(\App\Models\User::orderBy('created_at', 'desc')->take(7)->pluck('created_at')->map(function($d){ return $d->format('M d'); })->toArray())) !!},
            datasets: [{
                label: 'New Users',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                data: {!! json_encode(array_reverse(\App\Models\User::orderBy('created_at', 'desc')->take(7)->pluck('id')->toArray())) !!},
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endpush
