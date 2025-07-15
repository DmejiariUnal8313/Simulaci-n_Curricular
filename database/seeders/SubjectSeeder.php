<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // 1st semester
            ['code' => '4200910', 'name' => 'FUNDAMENTOS DE PROGRAMACIÓN', 'semester' => 1],
            ['code' => '1000004', 'name' => 'CÁLCULO DIFERENCIAL', 'semester' => 1],
            ['code' => '4100538', 'name' => 'INTRODUCCIÓN A LA ADMINISTRACIÓN DE SISTEMAS INFORMÁTICOS', 'semester' => 1],
            ['code' => '4100543', 'name' => 'INTRODUCCIÓN A LA EPISTEMOLOGÍA', 'semester' => 1],
            
            // 2nd semester
            ['code' => '4200916', 'name' => 'PROGRAMACIÓN ORIENTADA A OBJETOS', 'semester' => 2],
            ['code' => '4200919', 'name' => 'TEORÍA DE LA ADMINISTRACIÓN Y LA ORGANIZACIÓN I', 'semester' => 2],
            ['code' => '1000005', 'name' => 'CÁLCULO INTEGRAL', 'semester' => 2],
            ['code' => '4100539', 'name' => 'FUNDAMENTOS DE ECONOMÍA', 'semester' => 2],
            
            // 3rd semester
            ['code' => '4100548', 'name' => 'ESTRUCTURAS DE DATOS', 'semester' => 3],
            ['code' => '4200908', 'name' => 'ARQUITECTURA DE COMPUTADORES', 'semester' => 3],
            ['code' => '4100578', 'name' => 'ESTADÍSTICA I', 'semester' => 3],
            ['code' => '4100550', 'name' => 'SISTEMAS DE INFORMACIÓN', 'semester' => 3],
            ['code' => '1000003', 'name' => 'ÁLGEBRA LINEAL', 'semester' => 3],
            
            // 4th semester
            ['code' => '4100549', 'name' => 'ANÁLISIS Y DISEÑO DE ALGORITMOS', 'semester' => 4],
            ['code' => '4100552', 'name' => 'BASES DE DATOS I', 'semester' => 4],
            ['code' => '4100555', 'name' => 'PLANEACIÓN DE SISTEMAS INFORMÁTICOS', 'semester' => 4],
            ['code' => '4200909', 'name' => 'CONTABILIDAD Y COSTOS', 'semester' => 4],
            ['code' => '4100591', 'name' => 'INVESTIGACIÓN DE OPERACIONES I', 'semester' => 4],
            
            // 5th semester
            ['code' => '4100553', 'name' => 'INGENIERÍA DE SOFTWARE I', 'semester' => 5],
            ['code' => '4200915', 'name' => 'PROGRAMACIÓN CON TECNOLOGÍAS WEB', 'semester' => 5],
            ['code' => '4100541', 'name' => 'ADMINISTRACIÓN FINANCIERA', 'semester' => 5],
            
            // 6th semester
            ['code' => '4100554', 'name' => 'INGENIERÍA DE SOFTWARE II', 'semester' => 6],
            ['code' => '4100557', 'name' => 'SISTEMAS OPERATIVOS', 'semester' => 6],
            ['code' => '4200917', 'name' => 'SISTEMAS INTELIGENTES COMPUTACIONALES', 'semester' => 6],
            
            // 7th semester
            ['code' => '4100561', 'name' => 'AUDITORÍA DE SISTEMAS I', 'semester' => 7],
            ['code' => '4100558', 'name' => 'FUNDAMENTOS DE REDES DE DATOS', 'semester' => 7],
            ['code' => '4100544', 'name' => 'PSICOLOGÍA SOCIAL', 'semester' => 7],
            
            // 8th semester
            ['code' => '4200914', 'name' => 'MODELOS DE GESTIÓN DE TECNOLOGÍAS DE LA INFORMACIÓN', 'semester' => 8],
            ['code' => '4100562', 'name' => 'FORMULACIÓN Y EVALUACIÓN DE PROYECTOS INFORMÁTICOS', 'semester' => 8],
            ['code' => '4200911', 'name' => 'GERENCIA ESTRATÉGICA DEL TALENTO HUMANO', 'semester' => 8],
            ['code' => '4100560', 'name' => 'METODOLOGÍA DE LA INVESTIGACIÓN', 'semester' => 8],
            ['code' => '4200918', 'name' => 'TENDENCIAS EN ADMINISTRACIÓN DE SISTEMAS INFORMÁTICOS', 'semester' => 8],
            
            // 9th semester
            ['code' => '4200921', 'name' => 'ARQUITECTURA EMPRESARIAL', 'semester' => 9],
            ['code' => '4100563', 'name' => 'GERENCIA DE PROYECTOS TECNOLÓGICOS', 'semester' => 9],
            ['code' => '4100565', 'name' => 'LEGISLACIÓN TECNOLÓGICA', 'semester' => 9],
            
            // 10th semester
            ['code' => '4100565', 'name' => 'TRABAJO DE GRADO', 'semester' => 10],
            ['code' => '4100559', 'name' => 'PRÁCTICA', 'semester' => 10],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'code' => $subject['code'],
                'name' => $subject['name'],
                'semester' => $subject['semester'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
