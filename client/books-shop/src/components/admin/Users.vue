<template>
    <div>
    <v-card-text>
      <v-container class="container">
      <v-form v-model="valid.add" ref="form" lazy-validation>
        <v-layout >
        <v-flex xs12 >
                <div>ADD NEW USER</div>
                <div v-bind:class="messageClass">{{errMessage}}</div>
                <v-layout>
                  <v-flex xs5>
                  <v-text-field
                  name="input-1-3"
                  label="New Login"
                  v-model="newLogin"
                  single-line
                  :rules="nameRules"
                  required
                  dark
                  ></v-text-field>
                  </v-flex>
                  <v-flex xs4 offset-xs1>
                  <v-text-field
                  name="input-1-3"
                  label="New Password"
                  v-model="newPassword"
                  single-line
                  :rules="nameRules"
                  required
                  dark
                  ></v-text-field>
                  </v-flex>
                  <v-flex xs1 offset-xs1>
                  <v-btn
                  @click="addNewUser()"
                  :disabled="!valid.add"
                  >
                  Add
                  </v-btn>
                  </v-flex>

                </v-layout>
        </v-flex>
        </v-layout>
      </v-form>
      <v-form v-model="valid.edit" ref="form" lazy-validation>
        <p>CHANGE USER STATUS</p>
        <v-layout>
            <v-flex xs7>
            <v-select
              v-bind:items="itemsUsers"
              v-model="selectedUser"
              label="Users"
              @change="selectUser()"
              dark
            ></v-select>
          </v-flex>
          <v-flex xs3 offset-xs1>
             <v-select
              v-bind:items="itemsStatus"
              v-model="selectedStatus"
              item-value="id"
              item-text="name"
              label="Status"
              max-height="250"
              required
              dark
            ></v-select>
            </v-flex>
            <v-flex xs1 offset-xs1>
            <v-btn @click="sayHi()" :disabled="!valid.edit">Update</v-btn>
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
        newLogin: '',
        newPassword: '',
        selectedUser: [],
        selectedStatus: [],
        itemsUsers: [],
        itemsStatus: [{name: 'Active', id: '1'},{name: 'Disabled', id: '2'}],
        valid: {add: false, edit: false},
        errMessage: '',
        messageClass: 'text-danger',
        name: '',
        nameRules: [
        (v) => !!v || 'Name is required',
        (v) => v.length >= 5 || 'Name must be more than 5 characters',
        ],
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
      }
    },
    created(){
        this.getUsers()
    },
    methods: {
      sayHi(){
        console.log(this.selectedAuthor.name)
      },
      getUsers(){
        var self = this
        self.itemsUsers = []
        axios.get('http://rest/user1/books-shop/server/api/users/')
        .then(function (response) {
          
          var i = 0;
          while (i < response.data.length) {
              var user = {text: response.data[i].login, value: 
                            {login: response.data[i].login, 
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
      addNewUser() {
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid.add && self.newLogin != '' && self.newPassword != ''){
            self.checkUniqueness(self.newLogin, function(response){

              if (response == true){
                var data = new FormData();
                data.append('login', self.newLogin);
                data.append('password', self.newPassword);

                axios.post('http://rest/user1/books-shop/server/api/users/', data, self.config)
                .then(function (response) {
                    self.getUsers()       
                    self.messageClass='text-success'
                    self.errMessage = 'User has added'
                })
                .catch(function (error) {
                  console.log(error)
                })
              }
              else
                self.errMessage = 'Login is reserved'    
            })
          }else
            self.errMessage = 'All fields is required' 
      },
      editUser(){
        var self = this
        self.errMessage=''
        self.messageClass='text-danger'

        if (self.valid && self.selectedUser.id != ''){
          self.checkUniqueness(self.selectedUser.name).then(function(){

              var data = new URLSearchParams();
              data.append('id_user', self.selectedUser.id);
              data.append('name_user', self.selectedUser.name);

              axios.put('http://rest/user1/books-shop/server/api/users/', data, self.config)
              .then(function (response) {
                console.log(response.data)
                self.getUsers()           
                self.messageClass='text-success'
                self.errMessage = 'Author has edited'
              })
              .catch(function (error) {
                console.log(error)
              }) 

          }).catch(function () {
              self.errMessage = 'Login is reserved'
          })
        }
      },
      selectUser(){
        var self = this
        setTimeout(function(){
          for (let obj in self.itemsStatus){
            if (self.itemsStatus[obj].id == self.selectedUser.status){
              self.selectedStatus = self.itemsStatus[obj]
            }
          }
        },500)
      },
      checkUniqueness(newName, callback){
        var response = false
        if (newName && newName != ''){
          response = true
          for (let obj in this.itemsUsers){
            if (this.itemsUsers[obj].text == newName.trim())
              response = false
          }
        }
        setTimeout(function(){callback(response)}, 500)      
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
