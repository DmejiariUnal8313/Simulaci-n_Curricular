<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectOrderController extends Controller
{
    /**
     * Get the original order of subjects as defined in materias.txt
     */
    public function getOriginalOrder()
    {
        return [
            1 => [
                '4200910', // FUNDAMENTOS DE PROGRAMACIÓN
                '1000004', // CÁLCULO DIFERENCIAL
                '4100538', // INTRODUCCIÓN A LA ADMINISTRACIÓN DE SISTEMAS INFORMÁTICOS
                '4100543', // INTRODUCCIÓN A LA EPISTEMOLOGÍA
            ],
            2 => [
                '4200916', // PROGRAMACIÓN ORIENTADA A OBJETOS
                '4200919', // TEORÍA DE LA ADMINISTRACIÓN Y LA ORGANIZACIÓN I
                '1000005', // CÁLCULO INTEGRAL
                '4100539', // FUNDAMENTOS DE ECONOMÍA
            ],
            3 => [
                '4100548', // ESTRUCTURAS DE DATOS
                '4200908', // ARQUITECTURA DE COMPUTADORES
                '4100578', // ESTADÍSTICA I
                '4100550', // SISTEMAS DE INFORMACIÓN
                '1000003', // ÁLGEBRA LINEAL
            ],
            4 => [
                '4100549', // ANÁLISIS Y DISEÑO DE ALGORITMOS
                '4100552', // BASES DE DATOS I
                '4100555', // PLANEACIÓN DE SISTEMAS INFORMÁTICOS
                '4200909', // CONTABILIDAD Y COSTOS
                '4100591', // INVESTIGACIÓN DE OPERACIONES I
            ],
            5 => [
                '4100553', // INGENIERÍA DE SOFTWARE I
                '4200915', // PROGRAMACIÓN CON TECNOLOGÍAS WEB
                '4100541', // ADMINISTRACIÓN FINANCIERA
            ],
            6 => [
                '4100554', // INGENIERÍA DE SOFTWARE II
                '4100557', // SISTEMAS OPERATIVOS
                '4200917', // SISTEMAS INTELIGENTES COMPUTACIONALES
            ],
            7 => [
                '4100561', // AUDITORÍA DE SISTEMAS I
                '4100558', // FUNDAMENTOS DE REDES DE DATOS
                '4100544', // PSICOLOGÍA SOCIAL
            ],
            8 => [
                '4200914', // MODELOS DE GESTIÓN DE TECNOLOGÍAS DE LA INFORMACIÓN
                '4100562', // FORMULACIÓN Y EVALUACIÓN DE PROYECTOS INFORMÁTICOS
                '4200911', // GERENCIA ESTRATÉGICA DEL TALENTO HUMANO
                '4100560', // METODOLOGÍA DE LA INVESTIGACIÓN
                '4200918', // TENDENCIAS EN ADMINISTRACIÓN DE SISTEMAS INFORMÁTICOS
            ],
            9 => [
                '4200921', // ARQUITECTURA EMPRESARIAL
                '4100563', // GERENCIA DE PROYECTOS TECNOLÓGICOS
                '4100565', // LEGISLACIÓN TECNOLÓGICA
            ],
            10 => [
                '4100573', // TRABAJO DE GRADO
                '4100559', // PRÁCTICA
            ]
        ];
    }
    
    /**
     * Get original order as JSON for JavaScript
     */
    public function getOriginalOrderJson()
    {
        return response()->json($this->getOriginalOrder());
    }
}
