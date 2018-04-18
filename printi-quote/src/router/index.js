import Vue from 'vue'
import Router from 'vue-router'
import QuoteByVolume from '@/components/QuoteByVolume'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'QuoteByVolume',
      component: QuoteByVolume
    }
  ]
})
