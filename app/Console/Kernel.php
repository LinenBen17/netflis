<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Definir las aplicaciones de consola de Artisan.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\FetchMovies::class,  // Registra tu comando aquí
    ];

    /**
     * Definir los programas de tareas de la consola.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Aquí puedes agregar tareas programadas si es necesario
        $schedule->command('movies:fetch')->daily();
    }

    /**
     * Registra los comandos de la consola.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');  // Carga todos los comandos desde la carpeta Commands
    }
}
