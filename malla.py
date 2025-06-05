# Estructura base para la malla curricular
from typing import List, Dict, Optional
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

# Ejemplo de malla curricular codificada (agrega más asignaturas según la imagen)
malla_curricular: Dict[str, Asignatura] = {
    "Fundamentos de Programación": Asignatura(
        nombre="Fundamentos de Programación",
        semestre=1,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Fundamental",
        prerrequisitos=[]
    ),
    "Estructuras de Datos": Asignatura(
        nombre="Estructuras de Datos",
        semestre=2,
        creditos=4,
        horas_presenciales=4,
        horas_autonomas=8,
        tipologia="Obligatoria",
        prerrequisitos=["Fundamentos de Programación"]
    ),
    # ...agregar más asignaturas aquí...
}

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
