<!-- flujo-diagrama.vue -->
<template>
  <div ref="paper" style="border:1px solid #ccc; width:1200px; height:600px;"></div>
</template>

<script>



export default {
  props: {
    flujo: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    const joint = window.joint; // 👈 moverlo aquí

    console.log(joint)
    console.log(this.flujo)
    const graph = new joint.dia.Graph();

    const paper = new joint.dia.Paper({
      el: this.$refs.paper,
      model: graph,
      width: 1200,
      height: 600,
      gridSize: 10,
      drawGrid: true,
    });

    const nodes = {};
    const pasoWidth = 140;
    const pasoHeight = 50;
    let x = 50;
    let y = 50;

    this.flujo.pasos.forEach(paso => {
      const rect = new joint.shapes.standard.Rectangle();
      rect.position(x, y);
      rect.resize(pasoWidth, pasoHeight);
      rect.attr({
        body: { fill: '#007bff' },
        label: { text: paso.nombre, fill: 'white' }
      });
      rect.addTo(graph);
      nodes[paso.id] = rect;

      x += 200;
      if (x > 1000) {
        x = 50;
        y += 100;
      }
    });

    this.flujo.transiciones.forEach(transicion => {
      const link = new joint.shapes.standard.Link();
      link.source(nodes[transicion.origen]);
      link.target(nodes[transicion.destino]);
      link.labels([{
        position: 0.5,
        attrs: {
          text: { text: transicion.accion, fill: '#000' }
        }
      }]);
      link.addTo(graph);
    });
  }
}
</script>
