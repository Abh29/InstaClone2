<template>
  <div class="row">
    <div class="col-4" v-for="post in this.posts">
      <image-post
          :id="post.id"
          :show_link="post.show_link"
          :image="post.image"
          :thumbnail="post.thumbnail"
          :caption="post.caption"
          :likes="post.likes"
          :liked="post.liked"
          :liking_link="post.liking_link"
      ></image-post>
    </div>

    <div style="padding-top:300px"></div>
    <div class="row justify-content-center" v-if="this.more_available">
      <div class="btn btn-info" @click="loadMore()">load more</div>
    </div>
  </div>
</template>

<script>
import ImagePost from "./ImagePost.vue";
import axios from "axios";
export default {
  name: "ImagePostLoader",
  props: [
    'link',
  ],
  components: {
    ImagePost,
  },
  data() {
    return {
      'page' : 2,
      'posts': [],
      'more_available': true,
    }
  },
  methods: {
    loadMore() {
      let self = this;
      this.$http.get(this.link + '?page=' + this.page).then(function (response){
        self.page++;
        let i = 0;
        response.data.forEach((p) => {
          self.posts.push(p);
          ++i;
        });
        if (i < 12)
          self.more_available = false;
      }).catch((e) => {
        console.log(e.message);
      })
    }
  }

}
</script>

<style scoped>

</style>