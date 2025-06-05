# Simulador de Malla Curricular

Este proyecto permite simular y visualizar cambios en la malla curricular en el plan de estudios 4035 Administración de sistemas informáticos, facilitando la gestión y análisis de asignaturas, prerrequisitos y tipologías.

## Características principales
- Representación programática de la malla curricular (asignaturas, créditos, horas, tipología, prerrequisitos).
- Funciones para agregar, eliminar y modificar asignaturas y prerrequisitos.
- Visualización de la malla curricular como grafo dirigido usando NetworkX y Matplotlib.
- Base para simulaciones de impacto y análisis estadístico.

## Estructura del proyecto
- `malla.py`: Contiene la estructura de datos, funciones de manipulación y ejemplos de uso/pruebas.
- `visualizacion.py`: Permite graficar la malla curricular y sus relaciones de prerrequisitos.

## Requisitos
- Python 3.8+
- networkx
- matplotlib

Instala las dependencias con:
```bash
pip install networkx matplotlib
```

## Uso
1. Ejecuta `malla.py` para ver ejemplos de manipulación de la malla curricular.
2. Ejecuta `visualizacion.py` para visualizar la malla como un grafo.

## Próximos pasos sugeridos
- Completar la malla curricular con todas las asignaturas del plan.
- Implementar análisis estadístico y simulación de cambios.
- Agregar persistencia (guardar/cargar malla desde archivo).
- Evaluar impacto de cambios en estudiantes en curso y futuros.

---

Proyecto en desarrollo inicial. ¡Contribuciones y sugerencias son bienvenidas!
