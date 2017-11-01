<template>
  <v-app id="inspire">
    <!-- left menu all-->
    <v-navigation-drawer
      persistent
      clipped
      app
      v-model="drawer"
    >
      <v-list dense>
        <template v-for="(item, i) in items">
          <!-- left menu with action filter -->
          <v-list-group v-if="item.children" v-model="item.model" no-action>
            <v-list-tile slot="item" @click="">
              <v-list-tile-action>
                <v-icon>{{ item.model ? item.icon : item['icon-alt'] }}</v-icon>
              </v-list-tile-action>
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <v-list-tile
              v-for="(child, i) in item.children"
              :key="i"
              @click="filterBooks(item.type, child.id)"
            >
              <v-list-tile-action v-if="child.icon">
                <v-icon>{{ child.icon }}</v-icon>
              </v-list-tile-action>
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ child.name }}
                </v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list-group>

        <!-- left menu change view -->
        <div v-if="auth">
         <v-list-tile v-if="!item.children" @click="changeView(item.value)">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>
                {{ item.text }}
              </v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </div> 
        </template>
      </v-list>
    </v-navigation-drawer>

    <!-- Header text -->
    <v-toolbar
      color="blue darken-3"
      dark
      app
      clipped-left
      fixed
    >
      <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <span @click="filterBooks()" class="header-logo">BOOKS SHOP</span>
      </v-toolbar-title>
        <form v-if="!auth" @submit.prevent="checkLogIn">
          <div class="input-group auth-box">
            <div class="errMessageLogIn">{{errMessageLogIn}}</div>
            <input v-model="login" name="login" type="text"  placeholder=" Login" aria-describedby="sizing-addon2">
            <input v-model="password" name="password" type="password"  placeholder=" Password" aria-describedby="sizing-addon2">
            <button class="auth-btn" type="submit">Login</button>
            <button class="auth-btn" onClick='location.href="/#/registration"'>Sing Up</button>
          </div>
        </form>
        <div v-if="auth">
          <button v-if="(rank.admin)"class="logout-btn" onClick='location.href="/#/admin"'>Admin</button>
          <button class="logout-btn" v-on:click="logout">Logout</button>
          <div class="welcomeMessage">Welcome to SHOP, {{login}}</div>
        </div> 
    </v-toolbar>
    <main>
      <v-content>

      <!-- List of Books / Home page -->
      <div class="book-list" v-if="viewBooks">
      <div class="book-box" v-for="book in booksList" :book="book">
      <v-card> 
        <v-card-media
          :src="'../../static/images/'+book.images"
          height="250px"
        >
        </v-card-media>
        <v-card-title primary-title style="height:120px">
          <div class="book-head">
            <div class="title-book" style="height:30px">{{book.name}}</div>
            <div class="grey--text" style="height:40px">{{book.authors}}</div>
            <div class="title-book" style="height:20px">{{book.price}} EUR</div>
            <div v-if="auth">
              <v-btn v-if="book.add" @click="addToBag(book.id); book.add = !book.add" flat color="primary" style="width:30px">Add to bag</v-btn>
              <v-btn v-if="!book.add" @click="changeView('viewBag')" flat color="primary" style="width:30px">To Bag</v-btn>
            </div>
          </div>
        </v-card-title>
          <v-btn icon @click.native="book.show = !book.show">
            <v-icon>{{ book.show ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
          </v-btn>
        <div class="more-box">
        <v-slide-y-transition>
          <v-card-text v-show="book.show">
            <p class="grey--text">{{book.genres}}</p>
            {{book.description}}
          </v-card-text>
        </v-slide-y-transition>
        </div>
      </v-card>
      </div>
      </div>
    
      <!-- Copmonent Bag and Orders -->
      <bag-box v-if="viewBag && auth" :items="bag" :idUser="idUser" :discountUser="discountUser" v-on:createOrder="changeData"></bag-box>
      <order-box v-if="viewOrders && auth" :items="orders" :idUser="idUser"></order-box>

      </v-content>
    </main>

  </v-app>
</template>
<script>
  import axios from 'axios'
  import Bag from './Bag'
  import Order from './Orders'

  export default {
    data: () => ({
      viewBooks: true,
      viewBag: false,
      viewOrders: false,
      dialog: false,
      drawer: false,
      show: false,
      auth: false,
      errMessageLogIn: '',
      login: '',
      idUser: '',
      password: '',
      discountUser: '0',
      rank: {user: true, admin: false},
      booksList: [],
      book:{},
      bag: [],
      orders: [],
      items: [
        { icon: 'home', text: 'Home', value:'viewBooks', data: ''},
        { icon: 'shopping_cart', text: 'My Bag', value:'viewBag', data: ''},
        { icon: 'content_copy', text: 'My Orders', value:'viewOrders', data: 'getOrders'}
      ]
    }),
    config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
    props: {source: String},
    created () {
        this.checkUser()
        this.getGenres()
        this.getAuthors()
        this.getBooks()
    },
    methods:{
      sayHi(){
        console.log('Hello')
      },
      checkUser(){
        var self = this
        var id_user = JSON.parse(localStorage.getItem('id'))
        var hash = JSON.parse(localStorage.getItem('hash'))

        if (hash && id_user){ 
            var data = id_user + '/' + hash

            axios.get('http://rest/user1/books-shop/server/api/auth/' + data)
            .then(function(response){
               self.idUser = id_user;
               self.auth = true;
               self.getDataOfUser()
               return true
            })
           .catch(function (error) {
               self.auth = false
               return false
            })

        }else{
            self.auth = false
            return false
        }
      },
      getDataOfUser(){
          var self = this

          if(self.idUser){          
              var data = self.idUser

              axios.get('http://rest/user1/books-shop/server/api/users/' + data)
              .then(function(response){
                  self.rank[response.data.rank] = true;
                  self.login = response.data.login;
                  self.discountUser = response.data.discount;
              })
              .catch(function (error) {
                  self.auth = false
              })

              this.getBag()
              this.getOrders()
          }
      },
      checkLogIn() {
        var self = this
        if (self.login != '' && self.password != ''){ 

            var data = new URLSearchParams();
            data.append('login', self.login);
            data.append('password', self.password);

            axios.put('http://rest/user1/books-shop/server/api/auth/', data, self.config)
            .then(function(response){
            console.log(response.data);
               localStorage.setItem('hash', JSON.stringify(response.data.hash))
               localStorage.setItem('id', JSON.stringify(response.data.id_user))
               self.checkUser(function(){})
            })
           .catch(function (error) {
              self.errMessageLogIn = "Incorrect login or password"
            })
            //.catch(function (error) {
            //         console.log(error)
        }else{
            self.errMessageLogIn = "This values is required"
            return false
        }
      },
      logout(){
          localStorage.removeItem('hash')
          localStorage.removeItem('id')
          this.checkUser()
          this.changeView('viewBooks')      
      },
      getBag(){
        
          var self = this
          if (self.idUser != ''){

              this.bag=[]
              axios.get('http://rest/user1/books-shop/server/api/carts/' + self.idUser)
              .then(function (response) {

                  response.data.forEach(function(book){
                      self.bag[self.bag.length] = {
                          value: false,
                          selected: true,
                          id: book.id_book,
                          name: book.name,
                          price: book.price,
                          discount: book.discount,
                          count: book.count,
                          totalPrice: (book.count * (book.price * ((100 - book.discount)/100))).toFixed(2)
                      }
                  })

              }).catch(function (error) {
                  console.log('You cart is empty')
              })
          }
      },
      getOrders(){
          var self = this
          if (self.idUser != ''){

              this.orders=[]
              axios.get('http://rest/user1/books-shop/server/api/orders/' + self.idUser)
              .then(function (response) {

                  response.data.forEach(function(order){
                      self.orders[self.orders.length] = {
                          value: false,
                          infoBox: false,
                          selected: true,
                          idOrder: order.id_order,
                          date: order.date,
                          status: order.status,
                          booksInfo: self.convertBookInfo(order), 
                          totalPrice: order.total_order_price
                      }
                  })

              }).catch(function (error) {
                  console.log('You order is empty')
              })
          }
      },
      convertBookInfo(order){
          var orderInfo=[]
          order.books = order.books.split(', ')
          order.count = order.count.split(', ')
          order.prices = order.prices.split(', ')
          order.discount = order.books_discounts.split(', ')

          var i = 0;
          while (i < order.books.length) {
              orderInfo[orderInfo.length] = {
              name: order.books[i],
              count: order.count[i],
              price: order.prices[i],
              discount: order.discount[i],
              totalPrice: +order.count[i] * (+order.prices[i] * ((100 - order.discount[i])/100))
            }
            i++;
          }
          return orderInfo
      },
      addToBag(id){
        var self = this
        var post = true

        this.bag.forEach(function(book){
          if (book.id == id){
                
              var data = new URLSearchParams();
              data.append('id_book', id);
              data.append('count', +book.count+1);

              axios.put('http://rest/user1/books-shop/server/api/carts/'+self.idUser, data, self.config)
              .then(function(response){
                //console.log(response);
                 self.getBag()
              })
              .catch(function (error) {
                console.log(error)
              })
              post = false
            }
          })

          if(post){
            var data = new FormData();
            data.append('id_user', self.idUser);
            data.append('id_book', id);
            data.append('count', 1);

            axios.post('http://rest/user1/books-shop/server/api/carts/', data, self.config)
            .then(function (response) {
              //console.log(response);
               self.getBag()
            })
            .catch(function (error) {
              console.log(error)
            })
          }
      },
      getBooks(){
        var self = this
        axios.get('http://rest/user1/books-shop/server/api/books/')
        .then(function (response) {
          self.showBooks(response.data)
          self.items[self.items.length] = {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Books',
            type: 'books',
            model: false,
            children: self.booksList
          }
        })
      },
      getGenres(){
        var self = this
        axios.get('http://rest/user1/books-shop/server/api/genres/')
        .then(function (response) {
          self.items[self.items.length] = {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Genres',
            type: 'genres',
            model: false,
            children: response.data
          }
        })
      },
      getAuthors(){
        var self = this
        axios.get('http://rest/user1/books-shop/server/api/authors/')
        .then(function (response) {

          self.items[self.items.length] = {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Authors',
            type: 'authors',
            model: false,
            children: response.data
          }
        })
      },
      filterBooks(type="books", id="-"){
        var self = this        
        switch(type)
        {
          case 'authors':
          axios.get('http://rest/user1/books-shop/server/api/books/-/-/'+id)
          .then(function (response) {
              self.showBooks(response.data)
              //console.log(response.data)
          })
          break
          case 'genres': 
          axios.get('http://rest/user1/books-shop/server/api/books/-/-/-/'+id)
          .then(function (response) {
              self.showBooks(response.data)
          })
          break
          case 'books': 
          axios.get('http://rest/user1/books-shop/server/api/books/'+id)
          .then(function (response) {
              self.showBooks(response.data)
          })
          break
        }
      },
      changeData(value){
            if(value==true)
              this.getDataOfUser()
      },
      showBooks(data){
          data.forEach(function(book){
          book['show']=false
          book['add']=true
        })
        this.booksList = data
        //console.log(this.booksList)
      },
      changeView(activeView){
        this.viewBooks = false
        this.viewOrders = false
        this.viewBag = false
        this[activeView] = true
      }
   },
   components: {
      'bag-box' : Bag,
      'order-box' : Order
    } 
}

</script>
<style>
a :hover{
  text-decoration: none;
}

.header-logo{
cursor: pointer;
}

.book-head{
padding-top: 25px;
}

.book-head div{
  width: 150px;
  margin: 0;
  padding-top: 0;
  text-align:center;
}

.book-box{
  width: 180px;
  float: left;
  margin: 10px;
}

.title-book{
  font-size: 17px;
}

.card{
  height: 150px;
}

.more-box{
  background:white;
  position:absolute;
  z-index:4;
  margin-top: -2px;
  box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, .2);
}

.logout-btn{
  float:right;
  width: 70px;
  margin: 5px;
  padding: 3px;
  border: 2px solid;
  color: white;
  border-radius: 1px;
  transition: 0.2s;
}

.welcomeMessage{
  float:right;
  text-align: right;
  font-family: arial, verdana, sans-serif;
  font-size: 17px;
  margin: 5px;
  padding: 5px;
  width: 800px;
}

.errMessageLogIn{
  display: inline;
  text-align: right;
  margin: 5px;
  padding: 5px;
  width:450px;
}

.auth-box input{
  background-color: white;
  margin: 5px;
}

.auth-btn{
  width: 70px;
  margin: 5px;
  padding: 3px;
  border: 2px solid;
  color: white;
  border-radius: 1px;
  transition: 0.2s;
}
</style>