import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import Administration from '@/components/Administration'
import Registration from '@/components/Registration'
import Images from '@/components/Registration'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/registration',
      name: 'Registration',
      component: Registration
    },
    {
      path: '/admin',
      name: 'Administration',
      component: Administration
    }
  ]
})
