# 🎯 Sistema de Convalidaciones - Lógica Correcta

## 📋 Concepto Fundamental

El sistema de convalidaciones permite gestionar la **migración de estudiantes** desde una **malla curricular original** hacia una **nueva malla curricular**, determinando qué materias de su historial académico siguen siendo válidas.

## 🏗️ Escenario Base

### **Situación Inicial:**
- **Malla Original**: 35 materias
- **Estudiante Juan**: 50% completado = **18 materias aprobadas**
- **Nueva Malla**: 36 materias (puede ser diferente)

### **Problema a Resolver:**
¿Cuánto progreso conserva Juan al migrar a la nueva malla?

## 🔄 Tipos de Convalidación

### **1. ✅ Convalidación Directa**
**Concepto**: "Esta materia que ya viste equivale exactamente a esta materia de la nueva malla"

**Ejemplo**:
- Juan aprobó: `"Cálculo Diferencial"` (malla original)
- Nueva malla tiene: `"Cálculo Diferencial"` 
- **Resultado**: ✅ Se mantiene el crédito

### **2. ⭐ Libre Elección**
**Concepto**: "Esta materia que ya viste cuenta como créditos electivos en la nueva malla"

**Ejemplo**:
- Juan aprobó: `"Arte Digital"` (malla original)
- Nueva malla: No tiene equivalente directo
- **Resultado**: ⭐ Cuenta como electiva (crédito parcial)

### **3. ❌ No Convalidada**
**Concepto**: "Esta materia que ya viste NO cuenta para nada en la nueva malla"

**Ejemplo**:
- Juan aprobó: `"Tecnología Obsoleta"` (malla original)
- Nueva malla: Ya no considera válida esta materia
- **Resultado**: ❌ Crédito perdido

## 📊 Cálculo de Nuevo Progreso

### **Fórmula:**
```
Nuevo Progreso = (Materias Convalidadas) / (Total Materias Nueva Malla) × 100
```

### **Ejemplo Práctico:**

**Historial de Juan (18 materias aprobadas):**
- 12 materias → ✅ Convalidación directa
- 3 materias → ⭐ Libre elección (0.5 crédito c/u = 1.5 total)
- 3 materias → ❌ No convalidadas (perdidas)

**Cálculo:**
- **Créditos válidos**: 12 + 1.5 = 13.5 materias
- **Nueva malla**: 36 materias
- **Nuevo progreso**: 13.5 ÷ 36 = **37.5%**
- **Progreso perdido**: 50% - 37.5% = **12.5%**

## 🎯 Impacto Real en Estudiantes

### **Posibles Resultados:**

1. **📈 Progreso Mejor**: Si nueva malla es más pequeña
2. **📉 Progreso Menor**: Si nueva malla es más grande o hay materias no convalidadas
3. **⚖️ Progreso Igual**: Si convalidaciones compensan el cambio de tamaño

### **Factores de Impacto:**
- **Tamaño de la nueva malla** vs original
- **Cantidad de materias no convalidadas** (perdidas)
- **Materias nuevas** en la nueva malla
- **Prerrequisitos** que pueden bloquear progresión

## 💻 Implementación en el Sistema

### **Base de Datos:**
```sql
-- Convalidación directa
INSERT INTO subject_convalidations (
    external_subject_id,     -- Materia de la nueva malla
    internal_subject_code,   -- Materia de la malla original
    convalidation_type,      -- 'direct'
    equivalence_percentage   -- 100%
);

-- No convalidada (crédito perdido)
INSERT INTO subject_convalidations (
    external_subject_id,     -- Materia que ya no es válida
    internal_subject_code,   -- NULL (no mapea a nada)
    convalidation_type,      -- 'not_convalidated'
    equivalence_percentage   -- 0%
);
```

### **Análisis de Impacto:**
```php
// Calcular nuevo progreso
$convalidatedCredits = $directConvalidations->count() + 
                      ($freeElectives->count() * 0.5);
                      
$newProgress = ($convalidatedCredits / $newCurriculumSize) * 100;
$progressChange = $newProgress - $originalProgress;
```

## 🎨 Interfaz de Usuario

### **Indicadores Visuales:**
| Estado | Icono | Color | Descripción |
|--------|-------|-------|-------------|
| **Convalidada** | ✅ | Verde | Materia equivalente encontrada |
| **Libre Elección** | ⭐ | Azul | Crédito electivo |
| **No Convalidada** | ❌ | Rojo | Crédito perdido |
| **Nueva Materia** | ➕ | Naranja | Debe cursarse adicionalmente |

### **Dashboard de Impacto:**
```
┌─────────────────────────────────────────────────────────────┐
│ ANÁLISIS DE MIGRACIÓN CURRICULAR                           │
├─────────────────────────────────────────────────────────────┤
│ Estudiantes Analizados: 150                                │
│ Progreso Promedio Original: 45.2%                          │
│ Progreso Promedio Nuevo: 38.7%                             │
│ Impacto Promedio: -6.5% (pérdida de progreso)              │
├─────────────────────────────────────────────────────────────┤
│ ✅ Materias Convalidadas: 892                               │
│ ⭐ Créditos Electivos: 156                                  │
│ ❌ Créditos Perdidos: 234                                   │
│ ➕ Materias Nuevas Requeridas: 2                            │
└─────────────────────────────────────────────────────────────┘
```

## 🧪 Caso de Uso Completo

### **Escenario:**
Universidad actualiza malla de Sistemas agregando 2 materias nuevas y eliminando 1 obsoleta.

### **Datos:**
- **Malla Original**: 35 materias
- **Malla Nueva**: 36 materias (35 - 1 + 2)
- **Estudiantes**: 100 estudiantes activos

### **Configuración de Convalidaciones:**
- 32 materias: ✅ Convalidación directa
- 1 materia: ❌ No convalidada ("Tecnología Obsoleta")
- 2 materias: ➕ Nuevas ("Ética en TI", "Ciberseguridad")

### **Resultado Esperado:**
Los estudiantes conservarán la mayoría de su progreso, pero deberán cursar 2 materias adicionales y perderán créditos por la materia obsoleta.

---

**Actualizado**: Julio 30, 2025  
**Versión**: 2.0 (Lógica Corregida)  
**Estado**: ✅ Implementado y Funcional
