<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        Movie::create([
            'title' => 'Inception',
            'overview' => 'Un ladrón que roba secretos corporativos a través del uso de la tecnología de los sueños.',
            'poster_path' => 'https://image.tmdb.org/t/p/w500/qmDpIHrmpJINaRKAfWQfftjCdyi.jpg',
            'tmdb_id' => 27205
        ]);

        Movie::create([
            'title' => 'Interstellar',
            'overview' => 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio en un intento por salvar a la humanidad.',
            'poster_path' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
            'tmdb_id' => 157336
        ]);
    }
}
