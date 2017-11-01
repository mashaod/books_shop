<template>
  <v-app id="inspire" dark>
    <v-navigation-drawer
      clipped
      persistent
      v-model="drawer"
      enable-resize-watcher
      app
    >
      <v-list dense>
        <v-list-tile @click="changeView('viewAuthor')" class="block-title-menu-element">
          <v-list-tile-action>
            <div class="title-menu-element">Authors</div>
          </v-list-tile-action>
          <v-list-tile-content>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile @click="changeView('viewGenre')" class="block-title-menu-element">
          <v-list-tile-action>
            <div class="title-menu-element">Genres</div>
          </v-list-tile-action>
          <v-list-tile-content>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile @click="changeView('viewBook')" class="block-title-menu-element">
          <v-list-tile-action>
            <div class="title-menu-element">Books</div>
          </v-list-tile-action>
          <v-list-tile-content>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile @click="changeView('viewUser')" class="block-title-menu-element">
          <v-list-tile-action>
            <div class="title-menu-element">Users</div>
          </v-list-tile-action>
          <v-list-tile-content>
          </v-list-tile-content>
        </v-list-tile>
        <v-list-tile @click="changeView('viewOrder')" class="block-title-menu-element">
          <v-list-tile-action>
            <div class="title-menu-element">Orders</div>
          </v-list-tile-action>
          <v-list-tile-content>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
    <v-toolbar app fixed clipped-left>
      <v-toolbar @click.stop="drawer = !drawer" ><i class="menu-icon ion-navicon"></i><span class="head-text">ADMIN MENU</span></v-toolbar>
    </v-toolbar>
    <main>
      <v-content>
        <v-container fluid fill-height>
           <v-layout justify-center align-center>
            <div v-if="viewAuthor">
              <author-box></author-box>
            </div>
            <div v-if="viewGenre">
              <genre-box></genre-box>
            </div>
            <div v-if="viewBook">
              <book-box></book-box>
            </div>
            <div v-if="viewUser">
              <user-box></user-box>
            </div>
            <div v-if="viewOrder">
              <order-box></order-box>
            </div>
          </v-layout>           
        </v-container>
      </v-content>
    </main>
    <v-footer app fixed>
      <span>2017</span>
    </v-footer>
  </v-app>
</template>

<script>
      import Authors from './admin/Authors'
      import Genres from './admin/Genres'
      import Books from './admin/Books'
      import Users from './admin/Users'
      import Orders from './admin/Orders'

  export default {
    data: () => ({
      drawer: true,
      viewAuthor: false,
      viewGenre: false,
      viewBook: false,
      viewUser: false,
      viewOrder: false,
    }),
    props: {
      source: String
    },
    methods:{
      changeView(activeView){
        this.viewAuthor = false
        this.viewGenre = false
        this.viewBook = false
        this.viewUser = false
        this.viewOrder = false
        this[activeView] = true
      }
    },
    components: {
      'author-box' : Authors,
      'genre-box' : Genres,
      'book-box' : Books,
      'user-box' : Users,
      'order-box' : Orders
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}

.menu-icon{
  font-size: 40px;
}

.head-text{
  margin-left: 20px;
  font-size: 18px;
}

.block-title-menu-element{
  margin-top: 20px;
}

.title-menu-element{
  width: 100px;
  font-size: 15px;
  float: left;
}

</style>
