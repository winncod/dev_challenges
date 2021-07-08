<template>
  <div class="container-sm">
    <h1>Join to Error Board</h1>
    
    <join-form @on-join="joinHandler" title="Join to board error" subtitle="Please fill your name and enter the issue" />

  </div>
</template>

<script>
import JoinForm from './../components/JoinForm.vue';
import {joinIssue} from './../service/DataProvider'

export default {
  name: 'Join',
  components: {
    JoinForm
  },
  data(){
    return {
      
    }
  },
  methods:{
    async joinHandler(data){
      let resp = await joinIssue(data.username,data.issue)
      if(resp) //navigate to board issue
      {
          this.$router.push(`/board/${data.issue}`)
      }
    }
  }
}
</script>
