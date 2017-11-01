<template>
    <div>
    <v-card-text>
      <v-container class="container">
      <v-form v-model="valid.add" ref="form" lazy-validation>
        <v-layout >
        <v-flex xs12 >
                <div>ADD NEW AUTHOR</div>
                <div v-bind:class="messageClass">{{errMessage}}</div>
                <v-text-field
                name="input-1-3"
                label="New Author"
                v-model="newAuthor"
                single-line
                :rules="nameRules"
                required
                dark
                ></v-text-field>
                <v-btn
                @click="addNewAuthor()"
                :disabled="!valid.add"
                >
                Add
                </v-btn>
        </v-flex>
        </v-layout>
      </v-form>
      <v-form v-model="valid.edit" ref="form" lazy-validation> 
        <v-layout>
            <v-flex xs6>
            <v-select
              v-bind:items="items"
              v-model="selectedAuthor"
              label="Authors"
              dark
            ></v-select>
          </v-flex>
          <v-flex xs5 offset-xs1>
            <v-text-field
              name="input-3-3"
              label="Hint Text"
              :value="selectedAuthor.name"
              v-model="selectedAuthor.name"
              class="input-group--focused"
              :rules="nameRules"
              required
              dark
              single-line
            ></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout>
            <v-flex xs12>
            <v-btn @click="editAuthor()" :disabled="!valid.edit">Edit</v-btn>
            <v-btn @click="deleteAuthor()">Delete</v-btn>
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
        newAuthor: '',
        selectedAuthor: {name:'', id:''},
        items: [],
        valid: {add: false, edit: false},
        errMessage: '',
        messageClass: 'text-danger',
        name: '',
        nameRules: [
        (v) => !!v || 'Name is required',
        (v) => v.length >= 5 || 'Name must be more than 5 characters',
        (v) => v.search(/\d/) === -1 || 'The new name must haven\'t a number'
        ],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
      }
    },
    created(){
        this.getAuthors()
    },
    methods: {
      sayHi(){
        console.log(this.selectedAuthor.name)
      },
      getAuthors(){
        var self = this
        self.items = []
        axios.get('http://rest/user1/books-shop/server/api/authors/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var author = {text: response.data[i].name, value: {name: response.data[i].name, id: response.data[i].id}}
              self.items.push(author);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      addNewAuthor() {
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid){
          self.checkUniqueness(self.newAuthor, function(response){
            if(response == true){
              var data = new FormData();
              data.append('name_author', self.newAuthor);

              axios.post('http://rest/user1/books-shop/server/api/authors/', data, self.config)
              .then(function (response) {
                self.getAuthors()           
                self.messageClass='text-success'
                self.errMessage = 'New author has added'
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
      },
      editAuthor(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.selectedAuthor.id != ''){
          self.checkUniqueness(self.selectedAuthor.name, function(response){
            if(response == true){
              
              var data = new URLSearchParams();
              data.append('id_author', self.selectedAuthor.id);
              data.append('name_author', self.selectedAuthor.name);

              axios.put('http://rest/user1/books-shop/server/api/authors/', data, self.config)
              .then(function (response) {
                console.log(response.data)
                self.getAuthors()           
                self.messageClass='text-success'
                self.errMessage = 'Author has edited'
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
        else
          self.errMessage = 'Select the author first'
      },
      deleteAuthor(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.selectedAuthor.id != ''){

          axios.delete('http://rest/user1/books-shop/server/api/authors/'+ self.selectedAuthor.id)
          .then(function (response) {
            console.log(response.data)
            self.getAuthors()
            self.selectedAuthor = {name:'', id:''}           
            self.messageClass='text-success'
            self.errMessage = 'Author has deleted'
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        else
          self.errMessage = 'Select the author first'             
      },
      checkUniqueness(newName, callback){
        var self = this
        if (newName && newName != ''){
        axios.get('http://rest/user1/books-shop/server/api/authors/-/'+ newName)
          .then(function (response) {           
            if(response.data=='Empty')
              callback(true)
            else{         
              self.errMessage = 'New name must be unique'
              callback(false)
             }
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        else
          callback(false)
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
