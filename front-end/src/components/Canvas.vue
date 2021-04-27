<template>
  <div>
    <b-card class="">
      <div class="w-100 d-flex justify-content-between">
        <b-card :min-width="'480px'">
          <div class='d-flex justify-content-center mb-2'>
            <canvas :width='overview_width' :height='overview_width' id='overview-svg'>
              <img id="source"
                :src="url_svg_selected"
              >
            </canvas>
          </div>
          <b-card >
            <SvgTable @select_svg='selectSvg'/>
          </b-card>
        </b-card>
        <div class="col-sm-9">
          <canvas id='svg-viewer' :width='width' :height='height'
            v-on:mousedown="handleMouseDown" v-on:mouseup="handleMouseUp" v-on:mousemove="handleMouseMove"
            v-on:wheel="handleMouseWheel"
          >
            <img id="view_svg"
              :src="url_svg_background"
            >
          </canvas>
        </div>
      </div>
    </b-card>

    <b-card class="text-left" style="margin-bottom:0">
      <input type='range' v-model='current_scale' min='80' max='10000' class='mr-2'>
      <label class='text-right'> {{ current_scale }} % </label>
    </b-card>
  </div>
</template>

<script>
import SvgTable from './SvgTable.vue';
import axios from 'axios';

export default {
  name: 'svg-canvas',
  props: {
  },
  components: {
    SvgTable,
  },
  watch: {
    total_width () {
      this.updateOverview();
    },
    total_height () {
      this.updateOverview();
    },
    current_scale (newValue) {
      this.scale_radius = newValue / 100;
      // this.vueCanvas.setTransform(this.scale_radius, 0, 0, this.scale_radius, 0, 0);
      this.drawOverview();
    },
    focus () {
      this.drawOverview();
      console.log(this.width, ": Width");
    },
  },
  methods: {
    getStyle() {
      let style_str = 'height: ' + this.height.toString() + 'px;';
      // style_str += ' width: ' + this.width.toString() + 'px;'
      return style_str;
    },
    updateOverview() {
      let scaleX = this.overview_width / this.total_width;
      let scaleY = this.overview_height / this.total_height;
      let scale = scaleX < scaleY ? scaleX : scaleY;

      this.overview.setTransform(1, 0, 0, 1, 0, 0);
      this.overview.scale(scale, scale);
      let dx = 0;
      let dy = 0;
      if (scaleX < scaleY)
        dy = (this.total_width - this.total_height) / 2;
      else
        dy = (this.total_height - this.total_width) / 2;
      this.overview.translate(dx, dy);
      this.drawOverview();
    },
    drawOverview() {
      let total = this.total_width > this.total_height ? this.total_width : this.total_height;
      this.overview.clearRect(-total, -total, total * 2, total * 2);
      const image = document.getElementById('source');
      this.overview.drawImage(image, 0, 0);
      this.overview.beginPath();
      this.overview.globalAlpha = 0.5;
      this.overview.fillStyle = "green";
      this.overview.lineWidth = "10";
      this.overview.rect(this.focus.x, this.focus.y, this.focus_width * 100 / this.current_scale, this.focus_heighht * 100 / this.current_scale);
      this.overview.fillRect(this.focus.x, this.focus.y, this.focus_width * 100 / this.current_scale, this.focus_heighht * 100 / this.current_scale);
      this.overview.stroke();
    },
    updateView()
    {
      var clientRect = {
        x: this.focus.x,
        y: this.focus.y,
        width: this.width / this.scale_radius,
        height: this.height / this.scale_radius
      };
      var that = this;
      axios({
        url: "image",
        params: {id: this.id_selected, clientRect: clientRect, scale: this.scale_radius},
        method: "GET",
      }).then((response) => {
        if (response.status == 200) {
          this.url_svg_background = 'http://localhost:8000' + response.data.url.substring(1);
          const image = document.getElementById('view_svg');

          image.addEventListener('load', e => {
            that.vueCanvas.clearRect(0, 0, that.vueCanvas.canvas.width, that.vueCanvas.canvas.height);
            that.vueCanvas.drawImage(image, 0, 0);
          });
          // let img = new Image();
          // img.onload = function() {
          //   that.vueCanvas.clearRect(0, 0, that.vueCanvas.canvas.width, that.vueCanvas.canvas.height);
          //   that.vueCanvas.drawImage(img, 0, 0);
          // };
          // img.src = this.url_svg_background;
        }
      }).catch(err => {
        console.log(err, ": Error");
      });
    },
    handleMouseDown(e) {
      this.pre_focus.x = e.x;
      this.pre_focus.y = e.y;
    },
    handleMouseUp(e) {
      // console.log('mouseUp');
      this.pre_focus.x = 0;
      this.pre_focus.y = 0;
      this.asginImage();
    },
    asginImage()
    {
      if (this.id_selected < 0)
        return
      if (this.timer_id) {
        clearTimeout(this.timer_id);
      }
      this.timer_id = setTimeout(this.updateView, 300);
    },
    sayHi() {
      alert('Hello');
    },
    handleMouseMove(e) {
      if (e.buttons != 1)
        return;
      if (this.id_selected < 0)
        return
      if (this.pre_focus.x == 0 && this.pre_focus.y == 0) {
        this.pre_focus.x = e.x;
        this.pre_focus.y = e.y;
        return;
      }
      let tempFocus = {x: 0, y: 0};
      tempFocus.x = this.focus.x - (e.x - this.pre_focus.x) / this.scale_radius;
      tempFocus.y = this.focus.y - (e.y - this.pre_focus.y) / this.scale_radius;
      this.focus = tempFocus;

      this.pre_focus.x = e.x;
      this.pre_focus.y = e.y;
      this.asginImage();
    },
    handleMouseWheel(e) {
      let scale;
      if (e.wheelDeltaY > 0)
        scale = this.current_scale * 0.7;
      else
        scale = this.current_scale * 1.5;
      if (scale > 10000)
        scale = 10000;
      if (scale < 80)
        scale = 80;
      console.log(scale);
      this.current_scale = Math.floor(scale);
      this.asginImage();
    },
    selectSvg(item) {
      this.id_selected = item.id;
      if (item.id > -1) {
        this.total_height = item.height;
        this.total_width = item.width;
        this.url_svg_selected = item.avataPath;
      }
    },
    getImage() {

    },
    uploadFile() {

    }
  },

  mounted() {
    this.height = screen.availHeight * 0.8;
    this.width = screen.availWidth * 0.7;
    var c = document.getElementById("svg-viewer");
    var ctx = c.getContext("2d");
    this.vueCanvas = ctx;

    c = document.getElementById("overview-svg");
    ctx = c.getContext("2d");
    this.overview = ctx;

    this.focus_heighht = this.height;
    this.focus_width = this.width;
    this.updateOverview();
    this.drawOverview();
  },

  data() {
    return {
      overview_height: 260,
      overview_width: 260,
      height: 100,
      width: 100,
      vueCanvas: '',
      overview: '',
      scale_radius: 1.0,
      current_scale: 100,
      total_width: 1000,
      total_height: 1000,
      url_svg_selected: '',
      focus: {
        x: 0,
        y: 0,
      },
      focus_width: 100,
      focus_heighht: 100,

      pre_focus : {
        x: 0,
        y: 0
      },
      id_selected: -1,
      url_svg_background: '',
      timer_id: -1
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
canvas {
  /* border: 3px solid black; */
  border-style: double;
}
table {
  border: 1px solid black;
}
</style>
