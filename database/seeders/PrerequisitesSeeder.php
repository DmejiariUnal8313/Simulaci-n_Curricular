<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrerequisitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing prerequisites
        DB::table('subject_prerequisites')->delete();
        
        // Prerequisites mapping based on prerrequisitos.txt
        $prerequisites = [
            // 2nd semester
            '4200916' => ['4200910'], // PROGRAMACIÓN ORIENTADA A OBJETOS → Fundamentos de Programación
            '1000005' => ['1000004'], // CÁLCULO INTEGRAL → Cálculo Diferencial
            
            // 3rd semester  
            '4100548' => ['4200916'], // ESTRUCTURAS DE DATOS → Programación Orientada a Objetos
            '4200908' => ['4200910'], // ARQUITECTURA DE COMPUTADORES → Fundamentos de Programación
            '4100578' => ['1000005'], // ESTADÍSTICA I → Cálculo Integral
            '4100550' => ['4200919'], // SISTEMAS DE INFORMACIÓN → Teoría de la Administración
            
            // 4th semester
            '4100549' => ['4100548'], // ANÁLISIS Y DISEÑO DE ALGORITMOS → Estructuras de Datos
            '4100552' => ['4100548', '4200916'], // BASES DE DATOS I → Estructuras de Datos, POO
            '4100555' => ['4100550'], // PLANEACIÓN DE SI → Sistemas de Información
            '4100591' => ['1000003', '4100578'], // INVESTIGACIÓN DE OPERACIONES I → Álgebra Lineal, Estadística I
            
            // 5th semester
            '4100553' => ['4100549', '4100552'], // INGENIERÍA DE SOFTWARE I → Algoritmos, BD I
            '4200915' => ['4200916'], // PROGRAMACIÓN CON TECNOLOGÍAS WEB → POO
            '4100541' => ['4200909'], // ADMINISTRACIÓN FINANCIERA → Contabilidad y Costos
            
            // 6th semester
            '4100554' => ['4100553'], // INGENIERÍA DE SOFTWARE II → Ingeniería de Software I
            '4100557' => ['4200908'], // SISTEMAS OPERATIVOS → Arquitectura de Computadores
            '4200917' => ['4100549', '4100578'], // SISTEMAS INTELIGENTES → Algoritmos, Estadística I
            
            // 7th semester
            '4100561' => ['4100554', '4100555'], // AUDITORÍA DE SISTEMAS I → Ing. Software II, Planeación SI
            '4100558' => ['4100557'], // FUNDAMENTOS DE REDES → Sistemas Operativos
            
            // 8th semester
            '4200914' => ['4100561'], // MODELOS DE GESTIÓN DE TI → Auditoría de Sistemas I
            '4100562' => ['4100541', '4100555'], // FORMULACIÓN Y EVALUACIÓN → Adm. Financiera, Planeación SI
            '4100560' => ['4100578'], // METODOLOGÍA DE LA INVESTIGACIÓN → Estadística I
            '4200911' => ['4200919', '4100544'], // GERENCIA ESTRATÉGICA TH → Teoría Admin, Psicología Social
            
            // 9th semester
            '4200921' => ['4200914', '4100550'], // ARQUITECTURA EMPRESARIAL → Modelos Gestión TI, SI
            '4100563' => ['4100554', '4100562'], // GERENCIA DE PROYECTOS → Ing. Software II, Formulación Proyectos
            
            // 10th semester
            '4100565' => ['4100560'], // TRABAJO DE GRADO → Metodología de la Investigación
            '4100559' => [], // PRÁCTICA → (70-80% plan aprobado - handled by business logic)
        ];
        
        foreach ($prerequisites as $subjectCode => $prereqCodes) {
            foreach ($prereqCodes as $prereqCode) {
                DB::table('subject_prerequisites')->updateOrInsert(
                    [
                        'subject_code' => $subjectCode,
                        'prerequisite_code' => $prereqCode
                    ],
                    [
                        'subject_code' => $subjectCode,
                        'prerequisite_code' => $prereqCode,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
    }
}
