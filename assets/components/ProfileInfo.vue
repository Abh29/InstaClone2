<template>
  <div class="col-8 pt-4 pl-4">

    <div class="d-flex align-items-center">
      <div class="align-items-baseline">
        <h1> {{ userName }}</h1>
      </div>
      <div class="ms-4">
        <subscribe-button
            :link="subscribeLink"
            :subscribed="subscribed"></subscribe-button>
      </div>
    </div>

    <div class="d-flex">
      <div class="pe-5"><strong>{{ postCount }}</strong> posts</div>
      <div class="pe-5"><strong>{{ followersCount}}</strong> followers</div>
      <div><strong>{{ followingCount }}</strong> following</div>
    </div>

    <div class="pt-2 font-weight-bold">{{ title }}</div>
    <div>{{description}}</div>
    <div class=" font-weight-bold pb-3"><a href="#" style="color: rgba(var(--fe0,0,53,105),1);"> {{ profileUrl}} </a></div>

    <div class="pt-3">
      <a :href="editLink" >edit profile</a>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProfileInfo",
  props: [
    'userName',
    'subscribeLink',
    'subscribed',
    'postCount',
    'followersCount',
    'followingCount',
    'title',
    'description',
    'profileUrl',
    'editLink',
  ],
}
</script>

<style scoped>

</style>