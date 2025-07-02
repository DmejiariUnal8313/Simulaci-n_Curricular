import networkx as nx
import matplotlib.pyplot as plt
from mallaDict import malla_curricular

def layout_horizontal_topologico(malla):
    # Ordena los nodos de izquierda (iniciales) a derecha (finales) según nivel topológico
    import networkx as nx
    G = nx.DiGraph()
    for nombre, asignatura in malla.items():
        G.add_node(nombre)
        for pre in asignatura.prerrequisitos:
            G.add_edge(pre, nombre)
    orden = list(nx.topological_sort(G))
    niveles = {}
    for nodo in orden:
        if not malla[nodo].prerrequisitos:
            niveles[nodo] = 0
        else:
            niveles[nodo] = 1 + max(niveles[pre] for pre in malla[nodo].prerrequisitos)
    # Agrupa por nivel
    niveles_inv = {}
    for nodo, nivel in niveles.items():
        niveles_inv.setdefault(nivel, []).append(nodo)
    pos = {}
    for nivel, nodos in niveles_inv.items():
        for i, nodo in enumerate(sorted(nodos)):
            pos[nodo] = (nivel, -i)
    return pos

def graficar_malla(malla):
    G = nx.DiGraph()
    for nombre, asignatura in malla.items():
        G.add_node(nombre, semestre=asignatura.semestre, tipologia=asignatura.tipologia)
        for pre in asignatura.prerrequisitos:
            G.add_edge(pre, nombre)
    
    tipologia_color = {
        'Fundamental': 'lightgreen',
        'Obligatoria': 'skyblue',
        'Optativa': 'orange',
        'Libre Elección': 'violet',
        'Humanística': 'gray',
        'Trabajo de Grado': 'gold',
        'Práctica Profesional': 'brown'
    }
    node_colors = [tipologia_color.get(malla[n].tipologia, 'gray') for n in G.nodes()]
    
    pos = layout_horizontal_topologico(malla)
    plt.figure(figsize=(18, 8))
    nx.draw(G, pos, with_labels=True, node_color=node_colors, node_size=1500, font_size=8, arrowsize=15)
    plt.title('Malla Curricular - Grafo de Prerrequisitos (Ordenado por Semestre)')
    plt.axis('off')
    plt.show()

if __name__ == "__main__":
    graficar_malla(malla_curricular)