<template>
  <div class="postContainer">
    <div class="postImage">
      <v-lazy-image class="post-img" loading="lazy" :src="image" :src-placeholder="thumbnail" :alt="caption"></v-lazy-image>
      <a @click="showModal()" data-bs-toggle="modal" data-bs-target="#postModal"><span class="show-post fa fa-eye"></span></a>
      <a v-if="edit_link != null" @click="editModal()" data-bs-toggle="modal" data-bs-target="#postModal"><span class="edit-post fa fa-edit"></span></a>
      <a v-if="dell_link != null" @click="dellModal()" data-bs-toggle="modal" data-bs-target="#postModal"><span class="dell-post fa fa-remove"></span></a>
      <div class="img-overlay">
        <like-button :likesCount="likes" :liked="_liked" :liking_link="liking_link"></like-button>
      </div>
    </div>

    <div class="likeBtn">
        <span class="likeIcon"># {{ caption }}</span>
    </div>

  </div>
</template>

<script>
import LikeButton from "./LikeButton.vue";
import VLazyImage from "v-lazy-image";
export default {
  name: "ImagePost",
  components: [
    LikeButton,
    VLazyImage,
  ],
  props: [
      'id',
      'image',
      'thumbnail',
      'caption',
      'likes',
      'liked',
      'liking_link',
      'show_link',
      'edit_link',
      'dell_link',
  ],
  data() {
    return {
      'isLoading' : true,
      '_liked': this.liked !== "0",
    }
  },
  methods: {
    showModal() {
      this.$http.get(this.show_link).then(function (response) {
        $('#postModal .modal-body').html(response.data);
      }).catch(function (error){
        console.log(error.message);
      })

    },
    editModal() {
      this.$http.get(this.edit_link).then(function (response) {
        $('#postModal .modal-body').html(response.data);
      }).catch(function (error){
        console.log(error.message);
      })

    },
    dellModal() {
      $('#postModal .modal-body').html(`
        <div class="row justify-content-between alert alert-danger me-3 ms-3">
            <div class="col-10">
                <div class="text-center">Please confirm that you want to <i class="strong">DELETE</i> this post</div>
            </div>
            <div class="col-2">
                <a class="btn btn-danger" href="` + this.dell_link +  `">confirm</a>
            </div>
        </div>
      `);
    }
  }

}
</script>

<style scoped>
  .postContainer{
    width: 100%;
  }
  .postContainer:hover .post-img {
    opacity: 0.4;
  }
  .postContainer:hover .img-overlay{
    opacity: 1;
  }
  .postImage{
    position: relative;
  }
  .post-img{
    opacity: 1;
    display: block;
    width: 100%;
    height: auto;
    transition: .5s ease;
    backface-visibility: hidden;
  }
  .img-overlay{
    transition: .5s ease;
    opacity: 0;
    position: absolute;
    top: 70%;
    left: 50%;
    transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    text-align: center;
  }

  .show-post{
    position: absolute;
    top: 5%;
    right: 5%;
    opacity: 0;
    cursor: pointer;
    color: #0a53be;
  }

  .edit-post{
    position: absolute;
    top: 5%;
    right: 20%;
    opacity: 0;
    cursor: pointer;
    color: #0abe61;
  }

  .dell-post{
    position: absolute;
    top: 5%;
    right: 35%;
    opacity: 0;
    cursor: pointer;
    color: #d0142a;
  }

  .postImage:hover .show-post,
  .postImage:hover .edit-post,
  .postImage:hover .dell-post{
    opacity: 1;
    transition: .5s ease;
    transform: scale(2);
  }

</style>