<template>
  <div>
      <div class="btn btn-info" @click="subscribe()">{{btnText}}</div>
  </div>
</template>

<script>
export default {
  name: "SubscribeButton",
  props: {
    'link' : String,
    'subscribed': Boolean,
  },
  data() {
    return {
      subed: this.subscribed,
    }
  },
  methods: {
    async subscribe() {
        let self = this;
        this.$http.get(this.link).then(function (response) {
          console.log('response: ');
          self.subed = !self.subed;
        }).catch(function(error){
          console.log('error');
        });
    }
  },
  computed: {
    btnText (){
      return this.subed ? 'Unsubscribe' : 'subscribe';
    }
  }

}
</script>

<style scoped>

</style>