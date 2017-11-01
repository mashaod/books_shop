<template>
    <div>
    <v-card-text>
      <v-container class="container">
      <v-form v-model="valid" ref="form" lazy-validation>
        <v-layout>
        <v-flex xs12 >
            <div>CHOUSE BOOK</div>

              <v-select
              label="Select book for edit"
              v-bind:items="itemsBooks"
              v-model="selectBook"
              max-height="200"
              @chan1ge="getBookById(selectBook); action.add = false"
            ></v-select>

            <div>or <br /> ADD NEW BOOK</div>
            <div v-bind:class="messageClass">{{errMessage}}</div>
            
            <v-text-field
            name="input-1-3"
            label="Name book"
            v-model="newName"
            single-line
            :rules="textRules"
            required
            dark
            ></v-text-field>
            <v-text-field
              name="input-7-1"
              label="Description"
              multi-line
              v-model="description"
              :rules="textRules"
              required
            ></v-text-field>
            <v-flex xs3>
            <v-text-field
              label="Price"
              v-model="price"
              :rules="priceRules"
              required
            ></v-text-field>
            </v-flex>
            <v-flex xs3>
               <v-select
              v-bind:items="itemsDiscounts"
              v-model="selectedDiscount"
              item-value="id"
              item-text="name"
              label="Discount"
              max-height="250"
              required
              dark
            ></v-select>
            </v-flex>
          <v-loyaut
            <v-flex xs5>
            <v-select
              v-bind:items="itemsAuthors"
              v-model="selectedAuthors"
              label="Authors"
              multiple
              max-height="250"
              required
              dark
            ></v-select>
          </v-flex>
          <v-flex xs5>
            <v-select
              v-bind:items="itemsGenres"
              v-model="selectedGenres"
              label="Genres"
              multiple
              max-height="250"
              required
              dark
            ></v-select>
          </v-flex>
          <v-flex xs3>
            <v-text-field
            name="input-1-4"
            label="name_images.jpg"
            v-model="image"
            single-line
            :rules="imageRules"
            required
            dark
            ></v-text-field>
          </v-flex>
          <v-flex>
            <v-btn @click="addBook()" :disabled="!valid || !action.add">Add</v-btn>
            <v-btn @click="editBook()" :disabled="!valid || action.add">Edit</v-btn>
            <v-btn @click="deleteBook()" :disabled="action.add">Delete</v-btn>
            <v-btn @click="clear()">Clear</v-btn>
          </v-flex>

        </v-flex>
        </v-layout>
      </v-form>
      </v-container>
    </v-card-text>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    props: [''],
    data () {
      return {
        action: {add: true},
        newName: '',
        description: '',
        price: '',
        image: '',
        selectBook: '',
        selectedAuthors: [],
        selectedGenres: [],
        selectedDiscount:{},
        itemsAuthors: [],
        itemsGenres: [],
        itemsBooks: [],
        itemsDiscounts: [],
        valid: false,
        errMessage: '',
        messageClass: 'text-danger',
        name: '',
        textRules: [
        (v) => !!v || 'Field is required',
        (v) => v.length >= 5 || 'Text must be more than 5 characters'
        ],
        priceRules: [
        (v) => !!v || 'Price is required',
        (v) => v.search(/[a-zA-Z_]/) === -1 || 'The price must have only numbers'
        ],
        imageRules: [
        (v) => !!v || 'Image is required',
        (v) => v.search(/([a-zA-Z0-9_-]+\.[png|jpg])/) != -1 || 'The image must have .jpg or .png'
        ],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
      }
    },
    created(){
        this.getBooks()
        this.getAuthors()
        this.getGenres()
        this.getDiscounts()
    },
    watch: {
      selectBook: function() {
        this.getBookById(this.selectBook);
      }
    },
    methods: {
      sayHi(){
        //this.discount.
        //this.discount.text = 'discount_0'
        //console.log(this.selectBook)
      },
      getBooks(){
        var self = this
        self.itemsBooks = []
        axios.get('http://rest/user1/books-shop/server/api/books/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var books = {text: response.data[i].name, value: {name: response.data[i].name, id: response.data[i].id}}
              self.itemsBooks.push(books);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })        
      },
      getBookById(id) {
        console.log(id);
        var self = this;

        setTimeout(function(){
          axios.get('http://rest/user1/books-shop/server/api/books/'+self.selectBook.id)
          .then(function (response) {

            if(response.data=='Empty'){
              self.errMessage = 'We did\'t find this book'
            }else{
               self.newName = response.data.name
               self.description = response.data.description
               self.price = response.data.price
               self.image = response.data.images
               self.selectedAuthors = self.convertToArray(response.data.authors, response.data.authors_id)
               self.selectedGenres = self.convertToArray(response.data.genres, response.data.genres_id)

              for(let obj in self.itemsDiscounts){
                if(self.itemsDiscounts[obj].id == response.data.id_discount){
                  self.selectedDiscount = self.itemsDiscounts[obj]
                }
              }           
            }
          })
          .catch(function (error) {
                self.errMessage = 'Incorrect request'
                console.log(error)
          })  
        }, 500)
      },
      addBook(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.checkParams()){
          self.checkUniqueness(self.newName, function(response){
            if(response == true){
              var data = new FormData();
              
              data.append('name', self.newName);
              data.append('description', self.description);
              data.append('price', self.price);
              data.append('id_discount', self.selectedDiscount);
              data.append('images', self.image);
              data.append('id_authors', self.convertToString(self.selectedAuthors));
              data.append('id_genres', self.convertToString(self.selectedGenres));

              axios.post('http://rest/user1/books-shop/server/api/books/', data, self.config)
              .then(function (response) {
                self.getBooks()
                self.clear()        
                self.messageClass='text-success'
                self.errMessage = 'New Book has added'
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
        else
        self.errMessage = 'All fields is required'
      },
      editBook(){
      var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.checkParams() && self.selectBook.id){
          self.checkUniqueness(self.newName, function(response){
            if(response == true){
              var data = new URLSearchParams();
              
              data.append('id_book', self.selectBook.id);
              data.append('name', self.newName);
              data.append('description', self.description);
              data.append('price', self.price);
              data.append('id_discount', self.selectedDiscount.id);
              data.append('images', self.image);
              data.append('id_authors', self.convertToString(self.selectedAuthors));
              data.append('id_genres', self.convertToString(self.selectedGenres));
  
              axios.put('http://rest/user1/books-shop/server/api/books/', data, self.config)
              .then(function (response) {
                //console.log(response.data)          
                self.getBooks()
                self.clear()
                self.messageClass='text-success'
                self.errMessage = 'Book has edited'  
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
        else
        self.errMessage = 'All fields is required'  
      },
      deleteBook(){
        var self = this

          axios.delete('http://rest/user1/books-shop/server/api/books/'+self.selectBook.id)
          .then(function (response) {

            if(response.data=='Empty'){
              self.errMessage = 'We did\'t find this book'
            }else{        
              self.messageClass='text-success'
              self.errMessage = 'Book has deleted'  
              self.getBooks()
              self.clear()        
            }
          })
          .catch(function (error) {
                self.errMessage = 'Incorrect request'
                console.log(error)
          })       
      },
      getAuthors(){
        var self = this
        self.itemsAuthors = []
        axios.get('http://rest/user1/books-shop/server/api/authors/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var author = {text: response.data[i].name, value: {name: response.data[i].name, id: response.data[i].id}}
              self.itemsAuthors.push(author);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      getGenres(){
        var self = this
        self.itemsGenres = []
        axios.get('http://rest/user1/books-shop/server/api/genres/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var genre = {text: response.data[i].name, value: {name: response.data[i].name, id: response.data[i].id}}
              self.itemsGenres.push(genre);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      getDiscounts(){
        var self = this
        self.itemsDiscounts = []
        axios.get('http://rest/user1/books-shop/server/api/discounts/')
        .then(function (response) {
          
          var discount = response.data
          for(let obj in discount){
              self.itemsDiscounts.push(discount[obj])
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      checkUniqueness(newName, callback){
        var self = this
        if (newName && newName != ''){
        axios.get('http://rest/user1/books-shop/server/api/books/-/'+ newName)
          .then(function (response) {           
            if(response.data=='Empty')
              callback(true)
            else{         
              self.errMessage = 'New name must be unique2'
              callback(false)
             }
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        else
          callback(false)
      },
      checkParams(){
        if(this.newName != '' &&
          this.description != '' &&
          this.price != '' &&
          this.selectedDiscount.id != '' &&
          this.image != '' &&
          this.selectedAuthors[0].id &&
          this.selectedGenres[0].id)
          return true
        else
          return false
        
      },
      convertToString(array){
          var items = []
          var i = 0
          while (i < array.length) {
              items.push(array[i].id)
              i++
          }
          return items.join()
      },
      convertToArray(stringName, stringId, callback){
          var items = []
          var names = stringName.split(', ')
          var id = stringId.split(', ')

          var i = 0
          while(i < names.length){
            items.push({name: names[i], id: id[i]})
          i++
          }
          return items
      },
      clear(){
        this.action.add = true
        this.selectBook = ''
        this.newName = ''
        this.description = ''
        this.price = ''
        this.image = ''
        this.selectBook = ''
        this.selectedAuthors = []
        this.selectedGenres = []
        this.selectedDiscount = []
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

.container{
padding-top: 0px;
width:700px
}

.text-danger{
  color:red
}

.text-success{
  color:green
}
</style>
