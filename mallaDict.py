from malla import Asignatura
from typing import List, Dict, Optional

malla_curricular: Dict[str, Asignatura] = {
    # Semestre 1
    "Fundamentos de Programación": Asignatura(
        nombre="Fundamentos de Programación",
        semestre=1,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Cálculo Diferencial": Asignatura(
        nombre="Cálculo Diferencial",
        semestre=1,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Introducción a la Epistemología": Asignatura(
        nombre="Introducción a la Epistemología",
        semestre=1,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Humanística",
        prerrequisitos=[]
    ),
    "Teoría de la Administración y Organizaciones": Asignatura(
        nombre="Teoría de la Administración y Organizaciones",
        semestre=1,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Libre Elección I": Asignatura(
        nombre="Libre Elección I",
        semestre=1,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),
    "Inglés I": Asignatura(
        nombre="Inglés I",
        semestre=1,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Humanística",
        prerrequisitos=[]
    ),

    # Semestre 2
    "Estructuras de Datos": Asignatura(
        nombre="Estructuras de Datos",
        semestre=2,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Fundamentos de Programación"]
    ),
    "Cálculo Integral": Asignatura(
        nombre="Cálculo Integral",
        semestre=2,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Fundamental",
        prerrequisitos=["Cálculo Diferencial"]
    ),
    "Arquitectura de Computadores": Asignatura(
        nombre="Arquitectura de Computadores",
        semestre=2,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Fundamentos de Economía": Asignatura(
        nombre="Fundamentos de Economía",
        semestre=2,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Libre Elección II": Asignatura(
        nombre="Libre Elección II",
        semestre=2,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),
    "Inglés II": Asignatura(
        nombre="Inglés II",
        semestre=2,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Humanística",
        prerrequisitos=["Inglés I"]
    ),

    # Semestre 3
    "Programación Orientada a Objetos": Asignatura(
        nombre="Programación Orientada a Objetos",
        semestre=3,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Estructuras de Datos"]
    ),
    "Estadística I": Asignatura(
        nombre="Estadística I",
        semestre=3,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Sistemas de Información": Asignatura(
        nombre="Sistemas de Información",
        semestre=3,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Contabilidad y Costos": Asignatura(
        nombre="Contabilidad y Costos",
        semestre=3,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Libre Elección III": Asignatura(
        nombre="Libre Elección III",
        semestre=3,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),
    "Inglés III": Asignatura(
        nombre="Inglés III",
        semestre=3,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Humanística",
        prerrequisitos=["Inglés II"]
    ),

    # Semestre 4
    "Análisis y Diseño de Algoritmos": Asignatura(
        nombre="Análisis y Diseño de Algoritmos",
        semestre=4,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Programación Orientada a Objetos"]
    ),
    "Bases de Datos I": Asignatura(
        nombre="Bases de Datos I",
        semestre=4,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Estructuras de Datos"]
    ),
    "Álgebra Lineal": Asignatura(
        nombre="Álgebra Lineal",
        semestre=4,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Administración Financiera": Asignatura(
        nombre="Administración Financiera",
        semestre=4,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Libre Elección IV": Asignatura(
        nombre="Libre Elección IV",
        semestre=4,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),
    "Inglés IV": Asignatura(
        nombre="Inglés IV",
        semestre=4,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Humanística",
        prerrequisitos=["Inglés III"]
    ),
    # ...existing code...
    # Semestre 5
    "Ingeniería de Software I": Asignatura(
        nombre="Ingeniería de Software I",
        semestre=5,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Análisis y Diseño de Algoritmos"]
    ),
    "Bases de Datos II": Asignatura(
        nombre="Bases de Datos II",
        semestre=5,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Bases de Datos I"]
    ),
    "Investigación de Operaciones I": Asignatura(
        nombre="Investigación de Operaciones I",
        semestre=5,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=["Estadística I"]
    ),
    "Administración Financiera": Asignatura(
        nombre="Administración Financiera",
        semestre=5,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Libre Elección V": Asignatura(
        nombre="Libre Elección V",
        semestre=5,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),

    # Semestre 6
    "Ingeniería de Software II": Asignatura(
        nombre="Ingeniería de Software II",
        semestre=6,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Ingeniería de Software I"]
    ),
    "Sistemas Operativos": Asignatura(
        nombre="Sistemas Operativos",
        semestre=6,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Arquitectura de Computadores"]
    ),
    "Contabilidad y Costos": Asignatura(
        nombre="Contabilidad y Costos",
        semestre=6,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Administración Financiera II": Asignatura(
        nombre="Administración Financiera II",
        semestre=6,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=["Administración Financiera"]
    ),
    "Libre Elección VI": Asignatura(
        nombre="Libre Elección VI",
        semestre=6,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),

    # Semestre 7
    "Auditoría de Sistemas I": Asignatura(
        nombre="Auditoría de Sistemas I",
        semestre=7,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Ingeniería de Software II"]
    ),
    "Fundamentos de Redes de Datos": Asignatura(
        nombre="Fundamentos de Redes de Datos",
        semestre=7,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Sistemas Operativos"]
    ),
    "Psicología Social": Asignatura(
        nombre="Psicología Social",
        semestre=7,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Humanística",
        prerrequisitos=[]
    ),
    "Libre Elección VII": Asignatura(
        nombre="Libre Elección VII",
        semestre=7,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),

    # Semestre 8
    "Modelos de Gestión de Tecnologías de Información": Asignatura(
        nombre="Modelos de Gestión de Tecnologías de Información",
        semestre=8,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Auditoría de Sistemas I"]
    ),
    "Formulación y Evaluación de Proyectos": Asignatura(
        nombre="Formulación y Evaluación de Proyectos",
        semestre=8,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Administración Financiera II"]
    ),
    "Gerencia Estratégica del Talento Humano": Asignatura(
        nombre="Gerencia Estratégica del Talento Humano",
        semestre=8,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Libre Elección VIII": Asignatura(
        nombre="Libre Elección VIII",
        semestre=8,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),

    # Semestre 9
    "Arquitectura Empresarial": Asignatura(
        nombre="Arquitectura Empresarial",
        semestre=9,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Modelos de Gestión de Tecnologías de Información"]
    ),
    "Gerencia de Proyectos Tecnológicos": Asignatura(
        nombre="Gerencia de Proyectos Tecnológicos",
        semestre=9,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Formulación y Evaluación de Proyectos"]
    ),
    "Legislación Tecnológica": Asignatura(
        nombre="Legislación Tecnológica",
        semestre=9,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Obligatoria",
        prerrequisitos=[]
    ),
    "Libre Elección IX": Asignatura(
        nombre="Libre Elección IX",
        semestre=9,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),

    # Semestre 10
    "Trabajo de Grado": Asignatura(
        nombre="Trabajo de Grado",
        semestre=10,
        creditos=10,
        horas_presenciales=0,
        horas_autonomas=20,
        tipologia="Trabajo de Grado",
        prerrequisitos=["Gerencia de Proyectos Tecnológicos"]
    ),
    "Práctica": Asignatura(
        nombre="Práctica",
        semestre=10,
        creditos=8,
        horas_presenciales=0,
        horas_autonomas=16,
        tipologia="Práctica Profesional",
        prerrequisitos=[]
    ),
    "Libre Elección X": Asignatura(
        nombre="Libre Elección X",
        semestre=10,
        creditos=2,
        horas_presenciales=2,
        horas_autonomas=4,
        tipologia="Libre Elección",
        prerrequisitos=[]
    ),
}
