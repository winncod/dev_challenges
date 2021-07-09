<template>
  <div>
    <div class="user">
      Hi 
      <b v-if="you.name">{{you.name}} </b>
      <b v-if="!you.name">Invited  </b>
      <template v-if="!you.name">
        <a @click="joinAnoher()" href="#">Join to one Issue</a>
      </template>
      <template v-if="you.name">
        <a v-if="you.name" @click="disconect()" href="#">Disconect</a> or <a @click="joinAnoher()" href="#">Join another Issue</a>
      </template>
    </div>
    <div class="vote">
      <ul id="voteList">
        <li v-for="vote in validVotes"
            :key="vote.value"
            :class="{voted: you.value == vote.value && you.status != 'waiting'}"
            @click="emitVote(vote.value)">{{vote.text}}</li>
      </ul>
    </div>
    <div class="members">
      <h3>
        Voting issue # <b>{{issue.code}}</b> • Connected {{issue.members.length}}
      </h3>
      <ul id="memberList">
        <li :key="member.name" v-for="member in issue.members">
          <div class="status">{{member.status == 'voted' ? '✅' : ''}}</div>
          <div class="name">{{member.name}}</div>
          <div class="vote">{{member.status == 'voted' ? member.value : (member.status == 'passed' ? '?' : '-')}}</div>
        </li>
      </ul>
    </div>
    <div class="mt-1 mb-2 avg-card ">
      <b-card v-if="issue.status == 'reveal'" bg-variant="light" header="Issue avarage" class="text-center">
          <b-card-text>
              <b>{{issue.avg}}</b>
          </b-card-text>
      </b-card>
    </div>
    
    <b-modal id="modalJoinAnotherIssue" title="Join to another issue" hide-footer>
      <join-form :user="you.name" @on-join="joinHandler" title="" subtitle="Please enter the issue number" />
    </b-modal>
  </div>
</template>

<script>
import {getIssue,joinIssue,voteIssue,getAuth,setAuth,messageError} from './../service/DataProvider'
import JoinForm from './../components/JoinForm.vue';

export default {
  name: 'Board',
  components: {
    JoinForm
  },
  data() {
    return {
      issue: {
        code:'',
        status:'voting',
        members:[]
      },
      validVotes: [
        {value:1,text:'1'},
        {value:2,text:'2'},
        {value:3,text:'3'},
        {value:5,text:'5'},
        {value:8,text:'8'},
        {value:13,text:'13'},
        {value:20,text:'20'},
        {value:40,text:'40'},
        {value:0,text:'?'}
      ]
    };
  },
  computed: {
    you() {
      const auth = getAuth() ?? {name:''}       
      const current = this.issue.members.find((f)=>f.name == auth.name) 
      return current ?? {name:''}
    },
  },
  async mounted() {
    this.loadIssue();
  },
  methods: {
    async emitVote(vote) {
      if(this.you.name == '')
      {
        messageError('You must be joined to issue to vote')
        return
      }
      const resp = await voteIssue(this.issue.code,vote)
      if(resp) 
      {
        this.issue = resp
      }
    },
    async loadIssue() {
      const resp = await getIssue(this.$route.params.id)
      if(resp) 
      {
        this.issue = resp
      }else{
        this.$router.push(`/`)
      }
    },
    async disconect() {
      setAuth(null)
      this.$router.push(`/`)
    },
    async joinAnoher() {
      this.$bvModal.show('modalJoinAnotherIssue')
    },
    async joinHandler(data){
      this.$bvModal.hide('modalJoinAnotherIssue')
      if(this.$route.params.id != data.issue || this.you.name == '')
      {
        let resp = await joinIssue(data.username,data.issue)
        if(resp) //navigate to board issue
        {
            this.$router.push(`/board/${data.issue}`)
            this.loadIssue()
        }
      }
    }
  }
}
</script>


<style>
  #voteList {
    margin: 10px auto;
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    justify-content: space-around;
  }
  #voteList li {
    box-sizing: border-box;
    cursor: pointer;
    height: 100px;
    width: 100px;
    margin: 0;
    padding: 30px 0;
    border-radius: 9px;
    box-shadow: 2px 2px 2px #000;
    text-shadow: 1px 1px 2px #444;
    background: #e76f51;
    color: #fff;
    margin: 10px;
    font-size: 30px;
    transition: background-color 0.3s ease font-size 0.3s ease;
  }
  #voteList li.voted {
    font-size: 33px;
    background: #2a9d8f;
  }
  #memberList {
    list-style: none;
  }
  #memberList li {
    box-shadow: 2px 2px 2px #444;
    text-shadow: 1px 1px 1px #444;
    background: #e76f51;
    margin: 0.5em 0;
    padding: 1em;
    border-radius: 8px;
    display: flex;
    align-content: center;
  }
  #memberList li div {
    width: 50%;
    display: block;
    margin: auto;
  }
  #memberList li div.name {
    color: #FFF;
  }
  #memberList li div.vote {
    color: #FFF;
    text-shadow: none;
    font-size: 1.5em;
  }
  #issuenro {
    color: #2a9d8f;
    width: 75px;
    text-align: center;
  }
  .avg-card{
    padding-left: 2rem;
    color: #2a9d8f;
    text-shadow: 2px 2px 2px #ccc;
  }
  .avg-card .card-body{
    font-size: 32px;
  }
</style>
