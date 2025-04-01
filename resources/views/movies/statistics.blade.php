@extends('layouts.layout')

@section('title', 'Estadísticas | Netflis')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            background: #1f2937;
            /* Gris oscuro tipo Netflix */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s;
        }

        .chart-container:hover {
            transform: translateY(-5px);
        }

        .stats-card {
            background: #1f2937;
            /* Gris oscuro */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="px-4 py-8">
        <h1 class="text-3xl font-bold text-white mb-8 text-center">Estadísticas de Netflis</h1>

        <!-- Estadísticas del usuario -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-4">Tus estadísticas</h2>
            @if (!empty($userStats))
                <div class="stats-card">
                    <p class="text-lg text-gray-300 mb-4">
                        <span class="font-bold netflix-red">{{ $userStats[0]['total_interests'] }}</span>
                        películas marcadas como favoritas
                    </p>
                    <h3 class="text-xl font-semibold text-white mb-3">Tus géneros favoritos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($userGenres as $genre => $count)
                            <div class="flex items-center justify-between bg-gray-800 p-3 rounded-lg">
                                <span class="text-gray-200 font-medium">{{ $genre }}</span>
                                <span class="bg-red-600 text-white text-sm font-bold px-2 py-1 rounded-full">
                                    {{ $count }} {{ $count === 1 ? 'vez' : 'veces' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="stats-card text-center">
                    <p class="text-gray-400">Aún no has marcado películas. ¡Explora y encuentra tus favoritas!</p>
                </div>
            @endif
        </section>

        <!-- Géneros globales -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-4">Géneros más populares (Global)</h2>
            <div class="chart-container">
                <canvas id="genresChart" height="100"></canvas>
            </div>
        </section>

        <!-- Usuarios con más interacciones -->
        <section>
            <h2 class="text-2xl font-semibold text-white mb-4">Usuarios más activos</h2>
            <div class="chart-container">
                <canvas id="topUsersChart" height="100"></canvas>
            </div>
        </section>
    </div>

    <script>
        const colors = [
            'rgba(229, 9, 20, 0.7)', /* Rojo Netflix */
            'rgba(16, 185, 129, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(168, 85, 247, 0.7)'
        ];

        // Gráfico de géneros globales
        const genresCtx = document.getElementById('genresChart').getContext('2d');
        new Chart(genresCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($globalGenresStats as $stat)
                        '{{ $stat['_id'] }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Cantidad de selecciones',
                    data: [
                        @foreach ($globalGenresStats as $stat)
                            {{ $stat['count'] }},
                        @endforeach
                    ],
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827' /* Gris más oscuro para tooltips */
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' /* Líneas blancas sutiles */
                        },
                        ticks: {
                            color: '#d1d5db' /* Gris claro para etiquetas */
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#d1d5db'
                        }
                    }
                }
            }
        });

        // Gráfico de usuarios más activos
        const topUsersCtx = document.getElementById('topUsersChart').getContext('2d');
        new Chart(topUsersCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($topUsers as $user)
                        '{{ $user['_id']['username'] ?? 'Usuario ' . $user['_id']['user_id'] }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Interacciones',
                    data: [
                        @foreach ($topUsers as $user)
                            {{ $user['count'] }},
                        @endforeach
                    ],
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#d1d5db'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#d1d5db'
                        }
                    }
                }
            }
        });
    </script>
@endsection
