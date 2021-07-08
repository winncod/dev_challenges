import Vue from "vue";
import VueRouter from 'vue-router'

import Join from "./views/Join.vue";
import Board from "./views/Board.vue";

Vue.use(VueRouter)

const routes = [
  {
    path    : '*',
    redirect: '/',
  },
  {
    path: "/",
    name: "join",
    component: Join
  },
  {
    path: "/board/:id",
    name: "board",
    component: Board
  }
];

const router = new VueRouter({
  mode: "history",
  routes
});

export default router;