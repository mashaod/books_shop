<template>
    <div>
    <v-card-text>
      <v-container class="container">
      <v-form v-model="valid.add" ref="form" lazy-validation>
        <v-layout >
        <v-flex xs12 >
                <div>ADD NEW GENRE</div>
                <div v-bind:class="messageClass">{{errMessage}}</div>
                <v-text-field
                name="input-1-3"
                label="New Genre"
                v-model="newGenre"
                single-line
                :rules="nameRules"
                required
                dark
                ></v-text-field>
                <v-btn
                @click="addNewGenre()"
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
              v-model="selectedGenre"
              label="Genres"
              dark
            ></v-select>
          </v-flex>
          <v-flex xs5 offset-xs1>
            <v-text-field
              name="input-3-3"
              label="Hint Text"
              :value="selectedGenre.name"
              v-model="selectedGenre.name"
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
            <v-btn @click="editGenre()" :disabled="!valid.edit">Edit</v-btn>
            <v-btn @click="deleteGenre()">Delete</v-btn>
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
        newGenre: '',
        selectedGenre: {name:'', id:''},
        items: [],
        valid: {add: false, edit: false},
        errMessage: '',
        messageClass: 'text-danger',
        name: '',
        nameRules: [
        (v) => !!v || 'Name is required',
        (v) => v.length >= 5 || 'Genre must be more than 5 characters',
        (v) => v.search(/\d/) === -1 || 'The new genre must haven\'t a number'
        ],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
      }
    },
    created(){
        this.getGenres()
    },
    methods: {
      getGenres(){
        var self = this
        self.items = []
        axios.get('http://rest/user1/books-shop/server/api/genres/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var genre = {text: response.data[i].name, value: {name: response.data[i].name, id: response.data[i].id}}
              self.items.push(genre);
              i++
          }
        })
        .catch(function (error) {
              console.log(error)
        })
      },
      addNewGenre() {
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid){
          self.checkUniqueness(self.newGenre, function(response){
            if(response == true){
              var data = new FormData();
              data.append('name_genre', self.newGenre);

              axios.post('http://rest/user1/books-shop/server/api/genres/', data, self.config)
              .then(function (response) {
                self.getGenres()           
                self.messageClass='text-success'
                self.errMessage = 'New genre has added'
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
      },
      editGenre(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.selectedGenre.id != ''){
          self.checkUniqueness(self.selectedGenre.name, function(response){
            if(response == true){
              
              var data = new URLSearchParams();
              data.append('id_genre', self.selectedGenre.id);
              data.append('name_genre', self.selectedGenre.name);

              axios.put('http://rest/user1/books-shop/server/api/genres/', data, self.config)
              .then(function (response) {
                console.log(response.data)
                self.getGenres()           
                self.messageClass='text-success'
                self.errMessage = 'Genre has edited'
              })
              .catch(function (error) {
                console.log(error)
              })
            }             
          })
        }
        else
          self.errMessage = 'Select the genre first'
      },
      deleteGenre(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.selectedGenre.id != ''){

          axios.delete('http://rest/user1/books-shop/server/api/genres/'+ self.selectedGenre.id)
          .then(function (response) {
            console.log(response.data)
            self.getGenres()
            self.selectedGenre = {name:'', id:''}           
            self.messageClass='text-success'
            self.errMessage = 'Genre has deleted'
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        else
          self.errMessage = 'Select the genre first'             
      },
      checkUniqueness(newGenre, callback){
        var self = this
        if (newGenre && newGenre != ''){
        axios.get('http://rest/user1/books-shop/server/api/genres/-/'+ newGenre)
          .then(function (response) {           
            if(response.data=='Empty')
              callback(true)
            else{         
              self.errMessage = 'New Genre must be unique'
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
