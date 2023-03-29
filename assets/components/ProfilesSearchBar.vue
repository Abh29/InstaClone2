<template>
    <div id="navbar-search-autocomplete" class="me-5" @click="(ev) => {ev.stopPropagation()}">
      <div class="searchInput">
        <input type="search" class="" placeholder="Search" @input="suggest">
        <div class="resultBox">
          <li v-for="r in results"><i class="fa fa-user pe-2"></i><a :href="r.link">{{ r.userName }}</a></li>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: "ProfilesSearchBar",
  data() {
    return {
      results: []
    }
  },
  props: [
      'search_link',
  ],
  mounted() {
    $('body').on('click', function () {
      $('.searchInput').removeClass('active');
    });
  },
  methods: {
     suggest(event) {
      let self = this;
      let query = event.target.value;
      console.log(query);

        if (query === '') {
          self.results = [];
          $('.searchInput').removeClass('active');
        }
        else
          self.$http.get(self.search_link + '?query=' + query).then(function (response) {
            console.log(response.data, query);
            self.results = response.data;
            $('.searchInput').addClass('active');
          }).catch((e) => {
            console.log(e.message);
          })
    }

  },
}
</script>

<style scoped>
.searchInput{
  background: #fff;
  width: 100%;
  border-radius: 5px;
  position: relative;
  box-shadow: 0px 1px 5px 3px rgba(0,0,0,0.12);
  z-index: 100;
}

.searchInput input{
  height: 40px;
  width: 100%;
  outline: none;
  border: none;
  border-radius: 5px;
  padding: 0 60px 0 20px;
  font-size: 18px;
  box-shadow: 0px 1px 5px rgba(0,0,0,0.1);
}

.searchInput.active input{
  border-radius: 5px 5px 0 0;
}

.searchInput .resultBox{
  padding: 0;
  opacity: 0;
  pointer-events: none;
  max-height: 280px;
  overflow-y: auto;
  width: 100%;
}

.searchInput.active .resultBox{
  position: absolute;
  z-index: 100;
  padding: 10px 8px;
  background: white;
  opacity: 1;
  pointer-events: auto;
  box-shadow: 0px 1px 5px lightgray;
}

.resultBox li{
  list-style: none;
  padding: 8px 12px;
  display: none;
  width: 100%;
  cursor: default;
  border-radius: 3px;
}

.searchInput.active .resultBox li{
  display: block;
}

.searchInput.active .resultBox li a{
  text-decoration: none;
  color: #373b3e;
}

.resultBox li:hover{
  background: #efefef;
}

</style>