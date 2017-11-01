<template>
   <v-container  class="container" >
  <order-form></order-form>
  <div>{{ errMessage }}</div>
    <v-layout>
      <v-flex xs12>
        <v-data-table
        v-bind:headers="headers"
        :items="itemsOrders"
        hide-actions
        class="order_table"
        item-key="id_order"
        style="width:900px"
      >
      <template slot="items" scope="props">
        <tr :key="props.item.id_user" @click="props.item.infoBlock = !props.item.infoBlock">
          <td class="text-xs-center table-td">{{ props.item.id}}</td>
          <td class="text-xs-center table-td">{{ props.item.login }}</td>
          <td class="text-xs-center table-td">{{ props.item.total_order_price }}</td>
          <td class="text-xs-center table-td">{{ props.item.pay_name }}</td>
          <td class="text-xs-center table-td">
            <div v-if="!props.item.infoBlock">{{ props.item.status_order }}</div>
            <div v-if="props.item.infoBlock"> 
              <v-select
              v-bind:items="itemsStatuses"
              v-model="props.item.status.id"
              item-value="id"
              item-text="name"
              label="Status"
              max-height="150"
              max-width="40"
              required
              dark
              @change="changeStatus(props.item.id)"
              ></v-select>
            </div>
          </td>
          <td class="text-xs-center table-td">{{ props.item.date }}</td>
        </tr>
        <tr v-if="props.item.infoBlock" v-for="book in props.item.infoBooks" :book="book" style="background-color: grey">
          <td class="text-xs-center table-td">Name: {{book.name}}</td>
          <td class="text-xs-center table-td">Count: {{book.count}}</td>
          <td class="text-xs-center table-td">Price: {{book.price}} EUR</td>
          <td class="text-xs-center table-td">Discount: {{book.discount}}</td>
          <td class="text-xs-center table-td">Total Price: {{book.totalPrice}}</td>
          <td class="text-xs-center table-td"></td>
        </tr>
        </template>
        </v-data-table>
     </v-flex>
    </v-layout>
</v-container>
</template>

<script>
  import axios from 'axios'
  import addOrderForm from './addOrder'

  export default {
    props: [''],
    
    data () {
      return {
        pagination: {sortBy: 'date'},
        selected: [],
        itemsOrders: [],
        itemsStatuses:[],
        errMessage: '',
        valid: false,
        headers: [
          {text: 'Order Id', align: 'center', value: 'id_order'},
          { text: 'Login', align: 'center', value: 'login' },
          { text: 'Total Price', align: 'center', value: 'total_price' },
          { text: 'Payment', align: 'center', value: 'payment' },          
          { text: 'Status', align: 'center', value: 'status' },
          { text: 'Date', align: 'center', value: 'date' },
        ],
        ordersRules: [(v) => !!v || 'Field is required'],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
      }
    },
    created () {
      this.getOrders()
      this.getStatuses()
    },
    methods: {
      sayHi(){
        //console.log()
      },
      getOrders(){
          var self = this

              self.itemsOrders=[]
              axios.get('http://rest/user1/books-shop/server/api/orders/')
              .then(function (response) {

                for(let obj in response.data){
                        response.data[obj].infoBooks = self.convertBookInfo(response.data[obj])
                        response.data[obj].status = {name: response.data[obj].status_order, id: response.data[obj].id_status}
                        response.data[obj].infoBlock = false
                        self.itemsOrders.push(response.data[obj])
                        //console.log(response.data[obj])
                }

              }).catch(function (error) {
                  console.log('Orders list is empty')
              })
      },
      getStatuses(){
        var self = this
        self.itemsStatuses = []
        axios.get('http://rest/user1/books-shop/server/api/statuses/')
        .then(function (response) {
          
          var statuses = response.data
          for(let obj in statuses){
              self.itemsStatuses.push(statuses[obj])
          }
          //console.log(self.itemsStatuses)
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      changeStatus(idOrder){
        if (idOrder){
        var self = this
            for (let obj in self.itemsOrders){
              if (self.itemsOrders[obj].id == idOrder)
                setTimeout(function(){
                // var data = new URLSearchParams();
                
                // data.append('id_book', self.selectBook.id);
    
                // axios.put('http://rest/user1/books-shop/server/api/books/', data, self.config)
                // .then(function (response) {
                //   console.log(response.data)          
                //   self.getBooks()
                //   self.clear()
                //   self.messageClass='text-success'
                //   self.errMessage = 'Book has edited'  
                // })
                // .catch(function (error) {
                //   console.log(error)
                // })
              }, 500)
          }
        }else{
          this.errMessage = 'Choose order first'
        }

      },
      convertBookInfo(order){
          //console.log(order)
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
          //console.log(orderInfo)
          return orderInfo
      },
      changeSort (column) {
        if (this.pagination.sortBy === column) {
          this.pagination.descending = !this.pagination.descending
        } else {
          this.pagination.sortBy = column
          this.pagination.descending = false
        }
      }
    },
   components: {
      'order-form' : addOrderForm
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
