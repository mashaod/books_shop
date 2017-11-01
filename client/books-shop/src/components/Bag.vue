<template>
<div>
  <v-data-table
      v-model="selected"
      v-bind:headers="headers"
      v-bind:items="items"
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
    <table class="total-table">
      <tr>
        <td>COUNT</td>
        <td>SUM</td>
        <td>USER DISCOUNT</td>
        <td>TOTAL PRICE</td>
      </tr>
      <tr>
        <td>{{orderInfo.count}}</td>
        <td>{{orderInfo.sum}} EUR</td>
        <td>{{orderInfo.discount}} EUR</td>
        <td>{{orderInfo.price}} EUR</td>
      </tr>
    </table>

    <div class="text-xs-center pt-2">
      <v-btn v-if="createdBtn" color="primary" @click="createOrder(); createdBtn = !createdBtn">Create Order</v-btn>
      <v-btn v-if="!createdBtn" light disabled>Bag is empty</v-btn>
    </div>
    
  </div>
    
</div>
</template>
<script>
  import axios from 'axios'

  export default {
    props: ['items', 'idUser', 'discountUser'],
    
    data () {
      return {
        pagination: { sortBy: 'name' },
        selected: this.items.slice(),
        createdBtn: false,
        errMessage: '',
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
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
        orderInfo: {count: 0, sum: 0, discount: 0, price: 0},
        booksData: [],
      }
    },
    created: function () {
      this.toCountSum()
      if(this.items.length>0)
        this.createdBtn = true
    },
    methods: {
      toCountSum(){
        var self = this
        var total = {count: 0, sum: 0, discount: 0, price: 0}
        self.booksData = []
        console.log(self.discountUser)
        this.items.forEach(function(book){
            if(book.selected == true)
            {              
                total.count += +book.count
                total.sum += (book.price * +book.count)
                total.discount = total.sum * (self.discountUser/100)
                total.price = (+total.sum - total.discount).toFixed(2)
                self.booksData.push(book.id+"="+book.count)
            }
        })
        self.orderInfo = total
        console.log(self.orderInfo)
      },
      toggleAll () {
        if (this.selected.length) this.selected = []
        else this.selected = this.items.slice()
      },
      changeSort (column) {
        if (this.pagination.sortBy === column) {
          this.pagination.descending = !this.pagination.descending
        } else {
          this.pagination.sortBy = column
          this.pagination.descending = false
        }
      },
      changeCount(item, operand){
        var self = this
        if (operand == 'plus' || operand == "minus")
        {
          var book = self.items[item];
          var count = (operand == 'minus')?+book.count-1:+book.count+1;
          book.count = count<1?1:count

          var data = new URLSearchParams();
          data.append('id_book', book.id);
          data.append('count', +book.count);

          axios.put('http://rest/user1/books-shop/server/api/carts/'+ self.idUser, data, self.config)
          .then(function(response){
            console.log(response);
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        this.toCountSum()
      },
      createOrder(){
          var self = this
          var dataBooks = self.booksData.join('&')

          if(dataBooks && dataBooks != '')
          {
              var data = new FormData();
              data.append('id_user', self.idUser);
              data.append('data_books', dataBooks);
              data.append('id_payment', 1);
              data.append('total_order_price', self.orderInfo.price);

              axios.post('http://rest/user1/books-shop/server/api/orders/', data, self.config)
              .then(function (response) {
                  
                  axios.delete('http://rest/user1/books-shop/server/api/carts/'+ self.idUser)
                  .then(function (response) {
                          self.$emit('createOrder', true);             
                  })
                  .catch(function (error) {
                      console.log(error)
                  })
              })
              .catch(function (error) {
                  console.log(error)
              })
          }else{
            self.errMessage="First add product"
          }
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

.order-info{
  padding-top: 30px;
  padding-bottom: 20px;
  width: 100%;
  background-color: white;
  box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, .2);
}

.total-table{
  width: 100%;
  color: grey;
}
.counter{
  margin: 3px;
  float:left;
  font-size: 15px;
  cursor: pointer;
}

.count-minus{
  margin: 3px;
  float:left;
  font-size: 15px;
  cursor: pointer;
  padding-left: 178px;
}
.count-plus{
  margin: 3px;
  float:left;
  font-size: 15px;
  cursor: pointer;
}
</style>
