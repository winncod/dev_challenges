<template>
    <b-card :title="title" :sub-title="subtitle">
        <b-form @submit.prevent="onSubmit" @reset="onReset">
          <div class="row">
            <div class="col-6">
                <b-form-group id="username" label="Username" label-for="username">
                  <b-form-input id="username" :readonly="user!=null" v-model="join.username" placeholder="Please enter your User ID" required/>
                </b-form-group>  
            </div>  
            <div class="col-6">
                <b-form-group id="issue" label="Issue" label-for="issue">
                  <b-form-input  type="number" id="issue" v-model="join.issue"  placeholder="Please enter an issue to join it" required/>
                </b-form-group>
            </div>
          </div>
          <div class="text-rigth mt-2">
            <b-button type="submit">Join to Issue</b-button>
          </div>
          
        </b-form>
    </b-card>
</template>

<script>


export default {
  name: 'JoinForm',
  components: {
    
  },
  props: ['title','subtitle','user'],
  data(){
    return {
      join: {
        username: '',
        issue: ''
      }
    }
  },
  mounted(){
    this.join.username = this.user ?? ''
  },
  methods:{
    async onSubmit(event) {
      event.preventDefault()
      this.$emit('on-join',this.join)
    },
    onReset(event) {
      event.preventDefault()
      this.join.username = ''
      this.join.issue = ''
    }
  }
}
</script>
