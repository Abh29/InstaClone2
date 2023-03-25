<template>
  <div>
    <button class="button button-like" :class="{_liked}" @click="like()">
      <i class="fa fa-heart"></i>
      <span>{{ likes }}</span>
    </button>
  </div>
</template>

<script>
  export default {
    name: 'LikeButton',
    props: [
        'likesCount',
        'liked',
    ],
    data() {
      return {
        isActive: this.liked,
        likes: this.likesCount,
      }
    },
    methods: {
      increment() {
        this.count++
      },
      like() {
        this.isActive = !this.isActive;
        if (this.isActive)
          this.likes++;
        else
          this.likes--;
      },
    },
    computed: {
      _liked(){
        return this.isActive? 'liked' : '';
      }
    }

  }
</script>

<style scoped>

.button-like {
  border: 2px solid #8a8a8a;
  background-color: transparent;
  text-decoration: none;
  padding: 1rem;
  position: relative;
  vertical-align: middle;
  text-align: center;
  display: inline-block;
  border-radius: 3rem;
  color: #8a8a8a;
  transition: all ease 0.4s;
}

.button-like span {
  margin-left: 0.5rem;
}

.button-like .fa,
.button-like span {
  transition: all ease 0.4s;
}

.button-like:focus {
  background-color: transparent;
}

.button-like:focus .fa,
.button-like:focus span {
  color: #8a8a8a;
}

.button-like:hover {
  color: #cc4b37;
  border-color: #cc4b37;
  background-color: transparent;
}

.button-like:hover span {
  color: #cc4b37;
}
.button-like:hover .fa{
  color: #cc4b37;
}

.liked {
  background-color: #cc4b37;
  color: #fefefe;
  border-color: #cc4b37;
}

.liked span {
  color: #fefefe;
}

.liked .fa{
  color: #fefefe;
}

.liked:focus {
  background-color: #cc4b37;
}

.liked:focus .fa,
.liked:focus span {
  color: #fefefe;
}

.liked:hover {
  background-color: #cc4b37;
  color: #fefefe;
  border-color: #cc4b37;
}

.liked:hover .fa {
  color: #fefefe;
}
.liked:hover span {
  color: #fefefe;
}




</style>
