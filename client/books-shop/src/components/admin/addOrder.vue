<template>
   <v-container  class="container" >
  <div>CREATE ORDER</div>

    <v-data-table
      v-model="selected"
      v-bind:headers="headers"
      v-bind:items="miniBag"
      select-all
      v-bind:pagination.sync="pagination"
      item-key="name"
      class="elevation-1"
    >
    <template slot="headers" scope="props">
      <tr>
        <th>
        </th>
        <th v-for="header in props.headers" :key="header.text"
          :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
          @click="changeSort(header.value)"
        >
          <v-icon>arrow_upward</v-icon>
          {{ header.text }}
        </th>
      </tr>
    </template>
    <template slot="items" scope="props">
      <tr :active="props.selected">
        <td>
          <v-checkbox
            primary
            hide-details
            :input-value="props.selected"
            @click="props.selected = !props.selected; props.item.selected = !props.item.selected; toCountSum();"
          ></v-checkbox>
        </td>
        <td>{{ props.item.name }}</td>
        <td class="text-xs-center">{{ props.item.price }}</td>
        <td class="text-xs-center">{{ props.item.discount}}</td>
        <td><div class="counter-td"><div @click="changeCount(props.index, 'minus')" class="count-minus">-</div><div class="counter">{{ items[props.index].count }}</div><div @click="changeCount(props.index, 'plus')" class="count-plus">+</div></div></td>
        <td class="text-xs-center">{{ props.item.totalPrice }} EUR</td>
      </tr>
    </template>
  </v-data-table>

  <div class="order-info">
    <div class="text-xs-center pt-2">
      <v-btn v-if="createdBtn" color="primary" @click="createOrder(); createdBtn = !createdBtn">Create Order</v-btn>
      <v-btn v-if="!createdBtn" light disabled>Bag is empty</v-btn>
    </div>
  </div>




    <v-form v-model="valid" ref="form" lazy-validation>
    <v-layout>
      <v-flex xs4>
        <v-select
          label="Users"
          v-bind:items="itemsUsers"
          v-model="selectUser"
          :rules="ordersRules"
          required
          @change="clear()"
          dark
        ></v-select>
      </v-flex>
      <v-flex xs4 offset-xs1>
      <v-select
        label="Select book"
        v-bind:items="itemsBooks"
        v-model="selectBook"
        item-value="id"
        item-text="name"
        max-height="200"
        :rules="ordersRules"
        required
        @change="getBookById()"
      ></v-select>
      </v-flex>
      <v-flex xs2 offset-xs1>
      <div class="counter-td">
        <div @click="changeCount('minus')" class="counter">-</div>
        <div class="counter">{{ countBook }}</div>
        <div @click="changeCount('plus')" class="counter">+</div>
      </div>
      </v-flex>
      <v-btn @click="addBooktoCard()" :disabled="!valid">Add</v-btn>
    </v-layout>
  </v-form>
</v-container>
</template>

<script>
  import axios from 'axios'

  export default {
    props: [''],
    
    data () {
      return {
        itemsUsers: [],
        selectUser: [],
        itemsBooks: [],
        selectBook: [],
        countBook: 1,
        miniBag: [],
        selected: [],
        createdBtn: false,
        pagination: {sortBy: 'date'},
        errMessage: '',
        valid: false,
        headers: [
          {
            text: 'Name',
            align: 'left',
            value: 'name'
          },
          { text: 'Price', value: 'price' },
          { text: 'Discount %', value: 'discount' },
          { text: 'Count', value: 'count' },
          { text: 'Total Price', value: 'totalPrice' },
        ],
        ordersRules: [(v) => !!v || 'Field is required'],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
      }
    },
    created () {
      this.getUsers()
      this.getBooks()
    },
    methods: {
      sayHi(){
        console.log()
      },
      addBooktoCard(){
               this.miniBag[this.miniBag.length] = {
                 value: false,
                 selected: true,
                 id: this.selectBook.id,
                 name: this.selectBook.name,
                 price: this.selectBook.price,
                 discount: this.selectBook.discount,
                 count: this.countBook,
                 totalPrice: (this.countBook * (this.selectBook.price * ((100 - this.selectBook.discount)/100))).toFixed(2)
               }
            console.log(this.miniBag)
      },
      checkBag(){
        if (this.miniBag.length > 0){
            for (let obj in this.miniBag){
                if (this.miniBag[obj].id == this.selectBook.id){
                    this.miniBag[obj].count = this.countBook
                    return true
                }
            }
            setTimeout(function(){this.addBooktoCard()}, 200)
        }
        this.addBooktoCard()
      },
      getUsers(){
        var self = this
        self.itemsUsers = []
        axios.get('http://rest/user1/books-shop/server/api/users/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var user = {
                            text: response.data[i].login, value: {
                                login: response.data[i].login, 
                                id: response.data[i].id, 
                                status: response.data[i].status
                            }
                          }

              self.itemsUsers.push(user);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      getBooks(){
        var self = this
        self.itemsBooks = []
        axios.get('http://rest/user1/books-shop/server/api/books/')
        .then(function (response) {
          
          var books = response.data
          for(let obj in books){
              self.itemsBooks.push({name: books[obj].name, id: books[obj].id})
          }
        })
        .catch(function (error) {
              console.log(error)
        })        
      },
      getBookById(){
        var self = this

        setTimeout(function(){
          axios.get('http://rest/user1/books-shop/server/api/books/'+self.selectBook)
          .then(function (response) {

            if (response.data=='Empty'){
              self.errMessage = 'We did\'t find this book'
            }else{
               self.selectBook = {
                 id: response.data.id,
                 name: response.data.name,
                 price: response.data.price,
                 discount: response.data.discount
               }
               //console.log(self.selectBook)       
            }
          })
          .catch(function (error) {
                self.errMessage = 'Incorrect request'
                console.log(error)
          })  
        }, 500)
      },
      changeCount(operand){
        if (operand == 'minus' || operand == 'plus'){
          var count = (operand == 'minus')?this.countBook-1:this.countBook+1;
          this.countBook = count<1?1:count
        }
        else
          this.errMessage = 'Incorrect operation with counter'

      },
      changeSort (column) {
        if (this.pagination.sortBy === column) {
          this.pagination.descending = !this.pagination.descending
        } else {
          this.pagination.sortBy = column
          this.pagination.descending = false
        }
      },
      clear(){
          this.miniBag = []
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
*{ 
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

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
  text-decoration: none;
  color: #FFFFFF;
}

.table-td{
  width:130px;
}

.counter-td{
  padding-top: 22px;
}

.counter{
  margin: 3px;
  float:left;
  font-size: 15px;
  cursor: pointer;
}
</style>
