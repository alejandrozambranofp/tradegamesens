<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'color' => '#ff0000',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Programación',
                'slug' => 'programacion',
                'color' => '#00ff00',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Diseño Web',
                'slug' => 'diseno-web',
                'color' => '#0000ff',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Tutoriales',
                'slug' => 'tutoriales',
                'color' => '#ffff00',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Noticias',
                'slug' => 'noticias',
                'color' => '#ff00ff',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Opinión',
                'slug' => 'opinion',
                'color' => '#00ffff',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Recursos',
                'slug' => 'recursos',
                'color' => '#ff8800',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Laravel',
                'slug' => 'laravel',
                'color' => '#8800ff',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Vue.js',
                'slug' => 'vuejs',
                'color' => '#0088ff',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'General',
                'slug' => 'general',
                'color' => '#888888',
            ),
        ));
        
        
    }
}