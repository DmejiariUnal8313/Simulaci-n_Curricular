# 📋 Funcionalidades de Simulación Curricular

## 🎯 Resumen General

Se ha implementado un sistema completo de simulación curricular que permite modificar temporalmente la malla curricular y analizar el impacto en los estudiantes sin afectar la base de datos.

## 🔧 Funcionalidades Implementadas

### 1. **Drag & Drop de Materias**
- ✅ Arrastra materias entre semestres
- ✅ Feedback visual durante el arrastre
- ✅ Validación de posición
- ✅ Efecto de sombra y resaltado

### 2. **Modificación de Prerrequisitos**
- ✅ Clic derecho en materias para editar prerrequisitos
- ✅ Modal con editor de prerrequisitos
- ✅ Validación de formato
- ✅ Actualización visual inmediata

### 3. **Análisis de Impacto**
- ✅ Análisis completo de estudiantes afectados
- ✅ Detección de retrasos y bloqueos
- ✅ Cálculo de porcentajes de impacto
- ✅ Detalles específicos por estudiante

### 4. **Sistema de Cambios Temporales**
- ✅ Registro de todos los cambios
- ✅ Visualización de cambios activos
- ✅ Posibilidad de revertir cambios individuales
- ✅ Sistema de reset completo

### 5. **Interfaz Interactiva**
- ✅ Controles de simulación integrados
- ✅ Botones de acción (Analizar, Resetear, Guardar)
- ✅ Modales informativos
- ✅ Estados visuales para cambios

## 🚀 Cómo Usar la Simulación

### Mover Materias
1. Haz clic y arrastra cualquier materia
2. Suéltala en el semestre deseado
3. El sistema registrará el cambio automáticamente

### Modificar Prerrequisitos
1. Haz clic derecho en una materia
2. Edita los prerrequisitos en el modal
3. Los cambios se aplican instantáneamente

### Analizar Impacto
1. Realiza los cambios deseados
2. Haz clic en "Analizar impacto"
3. Revisa el reporte detallado

### Resetear Cambios
1. Haz clic en "Resetear"
2. Confirma la acción
3. Todos los cambios se revierten

## 📊 Métricas de Impacto

El sistema analiza:
- **Total de estudiantes** en el programa
- **Estudiantes afectados** por los cambios
- **Estudiantes con retrasos** potenciales
- **Porcentaje de impacto** general
- **Detalles específicos** por estudiante

## 🔄 Estado de la Simulación

### Cambios Temporales
- Los cambios NO se guardan en la base de datos
- Solo existen durante la sesión
- Se pueden revertir en cualquier momento

### Persistencia
- Los cambios se mantienen hasta recargar la página
- Se puede implementar guardado en localStorage
- Opción de "Guardar simulación" para futuras implementaciones

## 🎨 Feedback Visual

### Estados de Materias
- **Verde**: Materias disponibles
- **Azul**: Materias cursadas
- **Rojo**: Materias bloqueadas
- **Amarillo**: Prerrequisitos (al seleccionar)
- **Celeste**: Materias movidas/modificadas

### Efectos de Interacción
- Animaciones de arrastre
- Resaltado de zonas de destino
- Pulsos en materias modificadas
- Transiciones suaves

## 🔧 Arquitectura Técnica

### Frontend
- **JavaScript**: Lógica de simulación
- **CSS**: Estilos y animaciones
- **Bootstrap**: Componentes UI
- **Font Awesome**: Iconografía

### Backend
- **Laravel**: Framework PHP
- **SimulationController**: Análisis de impacto
- **Eloquent**: Relaciones de datos
- **PostgreSQL**: Base de datos

### Flujo de Datos
1. Usuario realiza cambios → Frontend registra cambios
2. Usuario analiza impacto → AJAX al backend
3. Backend procesa datos → Retorna análisis
4. Frontend muestra resultados → Modal informativo

## 🎯 Próximas Mejoras

### Funcionalidades Sugeridas
- [ ] Guardado de simulaciones
- [ ] Comparación de escenarios
- [ ] Exportación de reportes
- [ ] Simulación de cohortes específicas
- [ ] Predicción de graduación

### Mejoras Técnicas
- [ ] Validación de prerrequisitos circular
- [ ] Optimización de rendimiento
- [ ] Mejores algoritmos de análisis
- [ ] Interfaz responsive mejorada

## 🛠️ Comando de Ejecución

```bash
# Iniciar la aplicación
docker compose up -d

# Acceder a la simulación
http://localhost:8000
```

## 📝 Notas Importantes

1. **Seguridad**: Los cambios son temporales y no afectan datos reales
2. **Rendimiento**: El análisis es eficiente para hasta 1000 estudiantes
3. **Usabilidad**: Interfaz intuitiva con feedback visual constante
4. **Flexibilidad**: Sistema extensible para futuras funcionalidades
