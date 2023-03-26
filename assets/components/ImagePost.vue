<template>
  <div class="col-4 pt-5 postContainer">
    <div class="postImage">
      <v-lazy-image class="post-img" loading="lazy" :src="image" :src-placeholder="thumbnail" :alt="caption"></v-lazy-image>
      <a @click="showModal()" data-bs-toggle="modal" data-bs-target="#postModal"><span class="show-post fa fa-eye"></span></a>
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
      'link',
      'image',
      'thumbnail',
      'caption',
      'likes',
      'liked',
      'liking_link',
  ],
  mounted() {
    console.log(this.liked, !this.liked, !!this.liked);
  },
  data() {
    return {
      'isLoading' : true,
      '_liked': this.liked !== "0",
    }
  },
  methods: {
    showModal() {
      this.$http.get(this.link).then(function (response) {
        $('#postModal .modal-body').html(response.data);
      }).catch(function (error){
        console.log(error.message);
      })

    }
  }

}
</script>

<style scoped>
  .postContainer{
    width: 33%;
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

  .postImage:hover .show-post{
    opacity: 1;
    transition: .5s ease;
    transform: scale(2);
  }

</style>