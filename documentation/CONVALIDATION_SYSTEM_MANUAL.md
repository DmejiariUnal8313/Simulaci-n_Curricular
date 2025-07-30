# 🎯 Sistema de Convalidaciones - Manual de Usuario

## 📋 Descripción General

El Sistema de Convalidaciones permite cargar mallas curriculares externas desde Excel y realizar convalidaciones manuales de materias para estudiantes provenientes de otras instituciones educativas.

## 🚀 Características Principales

### ✅ Funcionalidades Implementadas

1. **Carga de Mallas Externas**
   - Importación desde archivos Excel (.xlsx, .xls)
   - Validación automática de formato
   - Almacenamiento organizado por institución

2. **Convalidación Manual**
   - Convalidación directa (materia equivalente específica)
   - Convalidación como libre elección
   - Sugerencias automáticas basadas en similitud de nombres

3. **Gestión Inteligente**
   - Porcentajes de equivalencia ajustables
   - Notas y observaciones por convalidación
   - Estados de aprobación/rechazo

4. **Reportes y Estadísticas**
   - Dashboard con estadísticas en tiempo real
   - Progreso de convalidación por malla
   - Exportación de reportes detallados

## 📊 Formato del Archivo Excel

### Columnas Requeridas

| Columna | Tipo | Requerida | Descripción |
|---------|------|-----------|-------------|
| `codigo` | Texto | ✅ Sí | Código único de la materia |
| `nombre` | Texto | ✅ Sí | Nombre completo de la materia |
| `creditos` | Número | ❌ No | Número de créditos (opcional) |
| `semestre` | Número | ❌ No | Semestre de la materia (opcional) |
| `descripcion` | Texto | ❌ No | Descripción adicional (opcional) |

### Ejemplo de Estructura

```
codigo    | nombre                           | creditos | semestre | descripcion
----------|----------------------------------|----------|----------|------------------
INF101    | Introducción a la Informática    | 3        | 1        | Conceptos básicos
MAT101    | Matemáticas I                    | 4        | 1        | Álgebra y cálculo
PRG101    | Programación I                   | 4        | 2        | Fundamentos prog.
```

## 🔧 Guía de Uso Paso a Paso

### Paso 1: Acceder al Sistema
1. En la simulación curricular, hacer clic en **"Realizar Convalidación"**
2. O navegar directamente a la sección **"Convalidaciones"** en el menú

### Paso 2: Cargar Malla Externa
1. Hacer clic en **"Realizar Convalidación"**
2. Completar la información de la malla:
   - **Nombre**: Ej. "Ingeniería de Sistemas - Universidad XYZ"
   - **Institución**: Nombre de la universidad
   - **Descripción**: Información adicional (opcional)
3. Cargar el archivo Excel con las materias
4. Hacer clic en **"Cargar Malla Curricular"**

### Paso 3: Realizar Convalidaciones
1. Revisar las materias importadas organizadas por semestre
2. Para cada materia, hacer clic en el botón **"Configurar"** (⚙️)
3. Elegir el tipo de convalidación:

#### **Convalidación Directa**
- Seleccionar una materia específica de nuestra malla
- Establecer porcentaje de equivalencia (0-100%)
- Agregar notas si es necesario

#### **Libre Elección**
- Marcar como créditos electivos
- No requiere materia específica equivalente
- Establece automáticamente equivalencia del 100%

### Paso 4: Usar Sugerencias Automáticas
1. Hacer clic en **"Ver Sugerencias"** (🪄)
2. El sistema analiza similitud de nombres
3. Seleccionar la sugerencia más apropiada
4. Ajustar equivalencia si es necesario

### Paso 5: Revisar y Exportar
1. Monitorear el progreso en el dashboard
2. Revisar estadísticas de convalidación
3. Exportar reporte final cuando esté completo

## 🎯 Tipos de Convalidación

### 🔄 Convalidación Directa
- **Uso**: Cuando existe una materia equivalente en nuestra malla
- **Ejemplo**: "Programación I" (externa) → "Fundamentos de Programación" (interna)
- **Beneficio**: Cumple prerrequisitos específicos

### ⭐ Libre Elección
- **Uso**: Cuando no hay equivalencia directa pero se reconoce como crédito
- **Ejemplo**: "Historia del Arte" → Libre Elección
- **Beneficio**: Suma créditos sin bloquear progresión

## 📈 Dashboard de Estadísticas

### Métricas Principales
- **Total de Mallas**: Número de mallas externas cargadas
- **Materias Externas**: Cantidad total de materias a convalidar
- **Convalidaciones**: Número de materias ya convalidadas
- **Pendientes**: Materias sin convalidar

### Progreso por Malla
- **Porcentaje de Avance**: Basado en materias convalidadas
- **Convalidaciones Directas**: Cantidad de equivalencias específicas
- **Libre Elección**: Cantidad de créditos electivos
- **Estados**: Pendiente, Aprobado, Rechazado

## 🔍 Sistema de Sugerencias

### Algoritmo de Similitud
El sistema utiliza análisis de texto para sugerir equivalencias:

1. **Normalización**: Elimina palabras comunes ("de", "la", "y", etc.)
2. **Similitud Léxica**: Compara caracteres y palabras
3. **Puntuación**: Calcula porcentaje de similitud (30% mínimo)
4. **Ranking**: Ordena sugerencias por relevancia

### Ejemplos de Funcionamiento
```
Materia Externa: "Programación Orientada a Objetos"
Sugerencias:
1. Programación Orientada a Objetos (95% similitud)
2. Fundamentos de Programación (65% similitud)
3. Estructuras de Datos (45% similitud)
```

## ⚙️ Administración Avanzada

### Gestión de Equivalencias
- **Porcentajes de Equivalencia**: Ajustar valor de conversión de notas
- **Notas Administrativas**: Documentar decisiones especiales
- **Estados de Aprobación**: Control de calidad de convalidaciones

### Casos Especiales
- **Materias Fragmentadas**: Una externa equivale a varias internas
- **Materias Combinadas**: Varias externas equivalen a una interna
- **Equivalencias Parciales**: Reconocimiento del 50-80% de valor

## 🛠️ Solución de Problemas

### Errores Comunes

#### **Error de Formato Excel**
- **Problema**: Columnas requeridas faltantes
- **Solución**: Verificar encabezados "codigo" y "nombre"
- **Prevención**: Usar plantilla proporcionada

#### **Materias Duplicadas**
- **Problema**: Códigos repetidos en la misma malla
- **Solución**: Verificar códigos únicos antes de cargar
- **Prevención**: Revisar archivo Excel previamente

#### **Sugerencias Vacías**
- **Problema**: No aparecen sugerencias automáticas
- **Solución**: Normal para materias muy específicas
- **Alternativa**: Usar convalidación manual

### Mejores Prácticas

1. **Preparación de Archivos**
   - Revisar datos antes de cargar
   - Usar nombres descriptivos consistentes
   - Incluir toda la información disponible

2. **Proceso de Convalidación**
   - Comenzar con sugerencias automáticas
   - Revisar equivalencias manualmente
   - Documentar decisiones especiales

3. **Control de Calidad**
   - Revisar porcentajes de equivalencia
   - Validar prereq conflictos potenciales
   - Mantener consistencia en criterios

## 📋 Lista de Verificación

### Antes de Cargar la Malla
- [ ] Archivo Excel tiene columnas "codigo" y "nombre"
- [ ] Códigos de materia son únicos
- [ ] Nombres están completos y claros
- [ ] Información adicional está incluida

### Durante la Convalidación
- [ ] Revisar sugerencias automáticas
- [ ] Verificar equivalencias directas
- [ ] Establecer porcentajes apropiados
- [ ] Documentar decisiones especiales

### Después de Completar
- [ ] Revisar estadísticas finales
- [ ] Exportar reporte de convalidaciones
- [ ] Validar casos especiales
- [ ] Documentar proceso para futuras referencias

## 🎉 Beneficios del Sistema

### Para Administradores
- **Eficiencia**: Proceso sistematizado y rápido
- **Consistencia**: Criterios uniformes de convalidación
- **Trazabilidad**: Registro completo de decisiones
- **Flexibilidad**: Adaptable a diferentes instituciones

### Para Estudiantes
- **Transparencia**: Proceso claro y documentado
- **Rapidez**: Convalidaciones más ágiles
- **Justicia**: Criterios objetivos y consistentes
- **Completitud**: Reconocimiento máximo de estudios previos

---

**Versión**: 1.0
**Fecha**: Julio 2025
**Desarrollado para**: Simulación Curricular - Administración de Sistemas Informáticos
