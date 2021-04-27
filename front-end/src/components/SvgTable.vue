<template>
  <div>
    <form>
    <div class="custom-file">
        <input type="file" ref="fileInput" class="custom-file-input" id="customFile" @input="pickFile"
          accept=".svg"
        >
        <label class="custom-file-label" for="customFile">Choose file</label>
    </div>
    </form>
    <br />
    <div v-for="(item, i) in fileList"
        :key="i"
        class='svg-item mb-3 d-flex justify-content-between'
    >
        <b-button @click='selectSvg(item)'>
            <img :src="item.avataPath" :width="'40px'" :height='"100%"'>
            <label class='ml-2'> {{item.name }}</label>
        </b-button>
        <div>
            <a
                class="btn btn-icon btn-light btn-sm mx-3 align-content-center pt-2"
                @click='deleteSvg(item)'
            >
                <span class="svg-icon svg-icon-md svg-icon-primary">
                <img src="../assets/svg/Trash.svg">
                </span>
            </a>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  mounted() {
    this.loadSvgs();

  },
  data() {
    return {
      fileList: [],
      uploadFile: '',
      fileName: '',
    };
  },
  methods: {
    selectSvg(item) {
      this.$emit('select_svg', item);
    },
    deleteSvg(item) {
      axios({
        url: "delete",
        params: {id: item.id},
        method: "POST",
      }).then((response) => {
        this.fileList = this.fileList.filter(value => item.id != value.id);
      }).catch(err => {
        console.log(err, ": Error");
      });
    },

    loadSvgs() {
      axios({
        url: "list",
        method: "GET",
      }).then((response) => {
        let lists = response.data.lists;
        this.fileList = lists.map(function (item) {
          let value = {
            id: item.id,
            name: item.name,
            avataPath: 'http://localhost:8000' + item.avata_path.substring(1),
            width: item.width,
            height: item.height
          };
          return value
        });
      }).catch(err => {
        console.log(err, ": Error");
      });
    },
    pickFile() {
      let input = this.$refs.fileInput;
      let file = input.files;

      this.uploadFile = this.$refs.fileInput.files[0];
      if ('image/svg+xml' != this.uploadFile.type)
        return ;
      let formData = new FormData();
      formData.append("file", this.uploadFile);
      axios({
        url: "upload",
        data: formData,
        params: {name: this.uploadFile.name},
        method: "POST",
      }).then((response) => {
        let value = {
          id: response.data.id,
          name: response.data.name,
          avataPath: 'http://localhost:8000' + response.data.avata_path.substring(1),
          width: response.data.width,
          height: response.data.height
        }
        this.fileList.push(value);
      }).catch(err => {
      });
    },
  }
};
</script>
<style scoped>
/* tile uploaded pictures */
.upload-list-inline >>> .ant-upload-list-item {
  float: left;
  width: 200px;
  margin-right: 8px;
}
.upload-list-inline >>> .ant-upload-animate-enter {
  animation-name: uploadAnimateInlineIn;
}
.upload-list-inline >>> .ant-upload-animate-leave {
  animation-name: uploadAnimateInlineOut;
}
.svg-item {
  /* box-shadow: 1px 1px 15px 5px rgba(0,0,0,0.7);
  -webkit-box-shadow: 1px 1px 15px 5px rgba(0,0,0,0.7);
  -moz-box-shadow: 1px 1px 15px 5px rgba(0,0,0,0.7); */
  border-style: double;
  border-radius: 3px;
}
</style>
