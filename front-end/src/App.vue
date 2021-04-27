<template>
  <div id="app" class='d-sm-flex justify-content-between'>
    <Canvas class='w-100 h-100'/>
  </div>
</template>

<script>
import Canvas from './components/Canvas.vue'
import HelloWorld from './components/HelloWorld.vue'

export default {
  name: 'app',
  components: {
    Canvas,
    HelloWorld
  },
  data() {
    return {
      svgs: [
      ],
      headers: [
        {key: 'name'}
      ],
      scale_svg: 100,
      corner_x: 0,
      corner_y: 0,
      scroll_number: 0,
      mini_maptx: 0,
    }
  },
  mounted() {
  },
  watch: {
  },
  methods: {
  }
}
</script>

<style>
  @import url('https://fonts.googleapis.com/css?family=Lato:400,700');

  body {
    background: #EEF1F4 !important;
    font-family: 'Lato', sans-serif !important;
  }

  .nav-background {
    background: #353535;
  }
</style>
