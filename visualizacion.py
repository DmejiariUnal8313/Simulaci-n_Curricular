import networkx as nx
import matplotlib.pyplot as plt
from mallaDict import malla_curricular

# Crear el grafo dirigido de la malla curricular
def graficar_malla(malla):
    G = nx.DiGraph()
    for nombre, asignatura in malla.items():
        G.add_node(nombre, semestre=asignatura.semestre, tipologia=asignatura.tipologia)
        for pre in asignatura.prerrequisitos:
            G.add_edge(pre, nombre)
    
    # Colores por tipología
    tipologia_color = {
        'Fundamental': 'lightgreen',
        'Obligatoria': 'skyblue',
        'Optativa': 'orange',
        'Libre Elección': 'violet'
    }
    node_colors = [tipologia_color.get(malla[n].tipologia, 'gray') for n in G.nodes()]
    
    pos = nx.spring_layout(G, seed=42)
    nx.draw(G, pos, with_labels=True, node_color=node_colors, node_size=1500, font_size=8, arrowsize=15)
    plt.title('Malla Curricular - Grafo de Prerrequisitos')
    plt.show()

if __name__ == "__main__":
    graficar_malla(malla_curricular)
