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
        <th lass="head-th" v-for="header in props.headers" :key="header.text"
          :class="['column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
          @click="changeSort(header.value)"
        >
          <v-icon>arrow_upward</v-icon>
          {{ header.text }}
        </th>
      </tr>
    </template>
    <template slot="items" scope="props">
      <tr :active="props.selected" @click="props.item.infoBox = !props.item.infoBox">
        <td class="text-xs-center table-td">{{ props.item.idOrder }}</td>
        <td class="text-xs-center table-td">{{ props.item.totalPrice }}</td>
        <td class="text-xs-center table-td">{{ props.item.status }}</td>
        <td class="text-xs-center table-td"></td>
        <td class="text-xs-center table-td">{{ props.item.date }} EUR</td>
      </tr>
      <tr v-if="props.item.infoBox" v-for="book in props.item.booksInfo" :book="book">
        <td>Name: {{book.name}}</td>
        <td>Count: {{book.count}}</td>
        <td>Price: {{book.price}} EUR</td>
        <td>Discount: {{book.discount}}</td>
        <td>Total Price: {{book.totalPrice}}</td>  
      </tr>
    </template>
  </v-data-table>
    
  </div>
    
</div>
</template>
<script>
  import axios from 'axios'

  export default {
    props: ['items'],
    
    data () {
      return {
        pagination: {
          sortBy: 'order_id'
        },
        selected: this.items.slice(),
        discountUser: 5,
        headers: [
          {text: 'Order Id', value: 'order_id'},
          { text: 'Total Price', value: 'total_price' },
          { text: 'Status', value: 'status' },
          { text: '', value: '' },
          { text: 'Date', value: 'date' },
        ],
        config: {
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        },
        orders: []
      }
    },
    created: function () {
      
    },
    created () {

    },
    methods: {
      changeSort (column) {
        if (this.pagination.sortBy === column) {
          this.pagination.descending = !this.pagination.descending
        } else {
          this.pagination.sortBy = column
          this.pagination.descending = false
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
  float:left;
  margin: 3px;
  font-size: 15px;
  cursor: pointer;
}

.count{
  margin: 3px;
  font-size: 15px;
  float: left;
}

.table-td{
  width: 20%;
}

</style>
