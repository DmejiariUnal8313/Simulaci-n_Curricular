# Lógica Correcta de Convalidaciones - Versión Corregida

## Conceptos Fundamentales

### 1. Tipos de Convalidación

#### **Convalidación Directa** (`direct`)
- **Qué es**: Materias de la nueva malla que tienen equivalencia directa con materias de la malla original
- **Condición**: El estudiante ya aprobó la materia equivalente en su malla original
- **Efecto**: El estudiante NO necesita cursar esta materia en la nueva malla
- **Ejemplo**: "Programación I" en ambas mallas

#### **Libre Elección** (`free_elective`)
- **Qué es**: Materias que se reconocen como créditos electivos
- **Condición**: Materias que no tienen equivalencia directa pero aportan valor académico
- **Efecto**: Se reconocen como créditos, pero con valor parcial
- **Ejemplo**: "Inglés" se reconoce como electivo

#### **Materia Nueva** (`not_convalidated`)
- **Qué es**: Materias de la nueva malla que NO existen en la malla original
- **Condición**: Son materias completamente nuevas del plan de estudios actualizado
- **Efecto**: El estudiante DEBE cursarlas para completar la nueva malla
- **Ejemplo**: "Inteligencia Artificial", "Ciberseguridad" en una malla renovada
- **🔥 IMPORTANTE**: NO son créditos perdidos, son requisitos adicionales

### 2. Cálculo de Progreso en Nueva Malla

```
Progreso en Nueva Malla = (Materias del Estudiante que se Convalidaron) / (Total Materias Nueva Malla) × 100

Donde:
- Materias Convalidadas ≤ Materias que el Estudiante Realmente Cursó
- NUNCA puede convalidar más de lo que cursó
- Solo cuenta lo que tiene equivalencia directa o libre elección válida
```

### ⚠️ **REGLA FUNDAMENTAL:**
**Un estudiante SOLO puede convalidar materias que realmente cursó y aprobó en su malla original.**

### 3. Análisis de Impacto

#### **Materias que el estudiante puede saltarse**:
- Convalidaciones directas
- Libre elección (parcialmente)

#### **Materias que el estudiante debe cursar**:
- Materias nuevas (not_convalidated)
- Materias de la nueva malla sin convalidar

#### **Créditos Perdidos** (calculado automáticamente):
- Materias que el estudiante aprobó en malla original
- Pero que NO existen en la nueva malla
- Se calculan como: `original_subjects - subjects_in_new_curriculum`

## Ejemplo Práctico

### Escenario: Migración a Malla de Ingeniería en Computación 2025

**Malla Original** (estudiante aprobó):
- Programación I ✅
- Matemáticas I ✅  
- Física I ✅
- Base de Datos ✅
- Redes I ✅

**Nueva Malla 2025**:
- Programación I (convalidación directa) ✅
- Matemáticas I (convalidación directa) ✅
- Física I (convalidación directa) ✅
- Base de Datos (convalidación directa) ✅
- **Inteligencia Artificial** (materia nueva) ❗ DEBE CURSAR
- **Ciberseguridad** (materia nueva) ❗ DEBE CURSAR
- **Machine Learning** (materia nueva) ❗ DEBE CURSAR

**Resultado**:
- ✅ Convalidadas: 4 materias (de las 5 que cursó)
- ❗ Debe cursar: 3 materias nuevas
- ❌ Materias sin equivalencia: 1 materia ("Redes I" no tiene equivalencia en nueva malla)
- 📊 Progreso: 4/7 = 57.1%

## ❓ ¿Por qué puede AUMENTAR el porcentaje al agregar materias nuevas?

Esta es una pregunta muy común y la respuesta está en cómo se calcula el progreso:

### Ejemplo Detallado:

**Escenario**: Estudiante con 30 materias aprobadas

**Malla Original** (40 materias total):
- Progreso = 30/40 = 75%

**Nueva Malla** (45 materias total, pero con convalidaciones):
- Materias convalidadas directas: 25 materias
- Materias nuevas: 20 materias (debe cursar)
- Progreso = 25/45 = 55.6%
- **Cambio: -19.4%** ⬇️

**Pero si hay MÁS convalidaciones...**

**Nueva Malla Optimizada** (30 materias total):
- Materias convalidadas directas: 25 materias
- Materias nuevas: 5 materias (debe cursar)
- Progreso = 25/30 = 83.3%
- **Cambio: +8.3%** ⬆️

### 🔑 **Factores que Afectan el Porcentaje**:

1. **Tamaño de la nueva malla**: Menos materias = mayor porcentaje
2. **Cantidad de convalidaciones**: Más convalidaciones = mayor porcentaje
3. **Eficiencia curricular**: Mallas más focalizadas pueden tener menos materias

### 📊 **Fórmula Completa**:

```
Cambio de Porcentaje = (Materias_Convalidadas / Total_Nueva_Malla) - (Materias_Aprobadas / Total_Original_Malla)

Positivo cuando: Materias_Convalidadas/Total_Nueva_Malla > Materias_Aprobadas/Total_Original_Malla
```

## 🆕 Nueva Funcionalidad: Explicación Detallada del Cambio de Progreso

### 🔍 **Análisis de Impacto Mejorado**

El sistema ahora incluye una explicación detallada de por qué el porcentaje de progreso de cada estudiante aumenta o disminuye al migrar a una nueva malla curricular.

### 📊 **Información Mostrada**:

1. **Cálculo Visual**:
   - Progreso original: X materias / Y total = Z%
   - Progreso nuevo: A materias / B total = C%

2. **Explicación Contextual**:
   - Por qué aumenta: Malla más eficiente, mejores convalidaciones
   - Por qué disminuye: Más requisitos, materias nuevas obligatorias
   - Factores específicos: Tamaño de malla, materias perdidas

3. **Detalles Específicos**:
   - Materias convalidadas exitosamente
   - Materias nuevas que debe cursar
   - Créditos de la malla original que ya no aplican

### 🎯 **Acceso a la Explicación**:

En el análisis de impacto, cada estudiante tiene un botón **"?"** que muestra:
- Modal con explicación detallada
- Gráficos visuales del cambio
- Texto explicativo personalizado
- Resumen de factores que influyen

### 💡 **Ejemplos de Explicaciones**:

**Progreso Aumenta (+15%)**:
```
✅ ¿Por qué AUMENTÓ el porcentaje?
• La nueva malla tiene MENOS materias (30 vs 40)
• Esto hace que cada materia convalidada tenga más peso porcentual
• El estudiante tiene una buena proporción de convalidaciones
• Sus materias aprobadas coinciden bien con la nueva malla
```

**Progreso Disminuye (-8%)**:
```
⚠️ ¿Por qué DISMINUYÓ el porcentaje?
• La nueva malla tiene MÁS materias (45 vs 40)
• Esto significa más requisitos para completar la carrera
• Debe cursar 5 materias nuevas que no existían antes
• 2 materias que aprobó ya no están en la nueva malla
```

## Implicaciones para el Sistema

### Dashboard de Estadísticas:
```
┌─────────────────────────────────────────┐
│ 📊 Resumen de Convalidaciones           │
├─────────────────────────────────────────┤
│ ✅ Convalidadas Directas: 15            │
│ ⭐ Libre Elección: 3                    │
│ ⚠️  Materias Nuevas: 8                  │
│ ⏳ Sin Configurar: 2                    │
└─────────────────────────────────────────┘
```

### Análisis de Impacto por Estudiante:
```
Estudiante: Juan Pérez
- Progreso Original: 75% (30/40 materias)
- Progreso Nueva Malla: 60% (24/40 materias)
- Cambio: -15% (debe cursar 6 materias nuevas)
- Materias Nuevas a Cursar: 6
- Créditos Perdidos: 3 materias
```

## Colores y Iconografía

- **Verde** (✅): Convalidación Directa - Materia aprobada
- **Azul** (⭐): Libre Elección - Reconocimiento parcial  
- **Amarillo/Naranja** (⚠️): Materia Nueva - Debe cursar
- **Gris** (⏳): Sin configurar - Pendiente de análisis

## Casos de Uso

### 1. Estudiante Avanzado (75% de progreso)
- Muchas convalidaciones directas
- Pocas materias nuevas que cursar
- Impacto: Positivo o neutral

### 2. Estudiante de Semestres Medios (50% de progreso)
- Mix de convalidaciones y materias nuevas
- Impacto: Variable según la cantidad de materias nuevas

### 3. Estudiante Inicial (25% de progreso)
- Pocas convalidaciones
- Muchas materias nuevas por cursar
- Impacto: Mayormente neutral (debe cursar la mayoría de todas formas)

## Conclusión

La lógica correcta distingue claramente entre:
- **Beneficio**: Materias que el estudiante se puede saltar (convalidadas)
- **Responsabilidad**: Materias nuevas que debe cursar (evolución curricular)
- **Pérdida**: Materias que cursó pero ya no son relevantes (créditos perdidos)

Esto refleja la realidad de la actualización curricular donde se agregan nuevas materias importantes que todos los estudiantes deben ver para estar al día con la evolución de la carrera.
