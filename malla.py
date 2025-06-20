# Estructura base para la malla curricular
from typing import List, Dict, Optional
from mallaDict import malla_curricular
import copy

class Asignatura:
    def __init__(self, nombre: str, semestre: int, creditos: int, horas_presenciales: int, horas_autonomas: int, tipologia: str, prerrequisitos: Optional[List[str]] = None):
        self.nombre = nombre
        self.semestre = semestre
        self.creditos = creditos
        self.horas_presenciales = horas_presenciales
        self.horas_autonomas = horas_autonomas
        self.tipologia = tipologia  # Fundamental, Obligatoria, Optativa, Libre Elección
        self.prerrequisitos = prerrequisitos or []

    def __repr__(self):
        return f"{self.nombre} (S{self.semestre}, {self.creditos}cr, {self.tipologia})"


def agregar_asignatura(malla: Dict[str, Asignatura], asignatura: Asignatura):
    malla[asignatura.nombre] = asignatura

def eliminar_asignatura(malla: Dict[str, Asignatura], nombre: str):
    if nombre in malla:
        del malla[nombre]
        # Eliminar de prerrequisitos de otras asignaturas
        for asig in malla.values():
            if nombre in asig.prerrequisitos:
                asig.prerrequisitos.remove(nombre)

def modificar_asignatura(malla: Dict[str, Asignatura], nombre: str, **kwargs):
    if nombre in malla:
        asignatura = malla[nombre]
        for key, value in kwargs.items():
            if hasattr(asignatura, key):
                setattr(asignatura, key, value)

def agregar_prerrequisito(malla: Dict[str, Asignatura], nombre: str, prerrequisito: str):
    if nombre in malla and prerrequisito in malla:
        if prerrequisito not in malla[nombre].prerrequisitos:
            malla[nombre].prerrequisitos.append(prerrequisito)

def eliminar_prerrequisito(malla: Dict[str, Asignatura], nombre: str, prerrequisito: str):
    if nombre in malla:
        if prerrequisito in malla[nombre].prerrequisitos:
            malla[nombre].prerrequisitos.remove(prerrequisito)

def estadisticas_malla(malla: Dict[str, Asignatura]):
    resumen = {
        'total_asignaturas': len(malla),
        'creditos_totales': 0,
        'creditos_por_tipologia': {},
        'asignaturas_por_semestre': {},
    }
    for asig in malla.values():
        resumen['creditos_totales'] += asig.creditos
        resumen['creditos_por_tipologia'].setdefault(asig.tipologia, 0)
        resumen['creditos_por_tipologia'][asig.tipologia] += asig.creditos
        resumen['asignaturas_por_semestre'].setdefault(asig.semestre, 0)
        resumen['asignaturas_por_semestre'][asig.semestre] += 1
    return resumen

def dependientes_de(malla: Dict[str, Asignatura], nombre: str):
    # Devuelve lista de asignaturas que dependen directa o indirectamente de 'nombre'
    dependientes = set()
    stack = [nombre]
    while stack:
        actual = stack.pop()
        for asig in malla.values():
            if actual in asig.prerrequisitos and asig.nombre not in dependientes:
                dependientes.add(asig.nombre)
                stack.append(asig.nombre)
    return list(dependientes)

# Ejemplo de uso: imprimir la malla
if __name__ == "__main__":
    print("--- Malla original ---")
    for nombre, asignatura in malla_curricular.items():
        print(asignatura)

    # Copia la malla original para pruebas
    malla_test = copy.deepcopy(malla_curricular)

    # Prueba: agregar una asignatura
    nueva_asig = Asignatura(
        nombre="Algoritmos Avanzados",
        semestre=3,
        creditos=3,
        horas_presenciales=3,
        horas_autonomas=6,
        tipologia="Optativa",
        prerrequisitos=["Estructuras de Datos"]
    )
    agregar_asignatura(malla_test, nueva_asig)

    # Prueba: modificar créditos
    modificar_asignatura(malla_test, "Estructuras de Datos", creditos=5)

    # Prueba: eliminar asignatura
    eliminar_asignatura(malla_test, "Fundamentos de Programación")

    print("\n--- Malla de prueba (modificada) ---")
    for nombre, asignatura in malla_test.items():
        print(asignatura)

    print("\n--- Estadísticas de la malla original ---")
    stats = estadisticas_malla(malla_curricular)
    for k, v in stats.items():
        print(f"{k}: {v}")

    # Simulación de impacto: ¿qué asignaturas dependen de 'Estructuras de Datos'?
    print("\nAsignaturas dependientes de 'Estructuras de Datos':")
    print(dependientes_de(malla_curricular, "Estructuras de Datos"))
