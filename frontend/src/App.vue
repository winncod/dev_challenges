<template>
  <div id="app">
    <b-overlay :show="loading" rounded="sm">
      <h1>♠ Planning Poker Demo♠</h1>
      <div id="logo"></div>
      <h2>Hiring Challenge 👋</h2>
      <notifications group="main" />
      <div id="container">
        <router-view></router-view>
      </div>
      <template #overlay>
        <div class="text-center">
          <b-icon icon="stopwatch" font-scale="3" animation="cylon"></b-icon>
          <p id="cancel-label">Please wait...</p>
        </div>
      </template>
    </b-overlay>
  </div>
</template>

<script>

export default {
  name: 'App',
  computed:{
    loading(){
      return this.$store.state.loading
    }
  }
}
</script>

<style>
body {
  box-sizing: border-box;
  font-family: Arial;
  margin: 0;
  background: #fff;
  text-align: center;
}
#logo {
  margin: auto;
  height: 35px;
  width: 219px;
  background: url(https://wkncdn.com/newx/assets/build/img/workana-logo-2x.9e13d14c2.png);
  background-repeat: no-repeat;
  background-size: cover;
}
#container {
  max-width: 550px;
  margin: auto;
}
h1 {
  margin: 0px;
  color: #e76f51;
  text-shadow: 2px 2px 2px #ccc;
}
h2 {
  margin: auto;
  color: #e76f51;
  font-size: 1em;
  text-shadow: 1px 1px 2px #ccc;
  text-align: center;
}
h3 {
  color: #2a9d8f;
  text-shadow: 2px 2px 2px #ccc;
}

</style>
