<template>
    <div class="register-page">
            <div v-if="successMessage">
                <h4>{{login}}, WELCOME to BOOK SHOP</h4>
                <div class="register-btn" onClick='location.href="/#/"'><button >Back to shop</button></div>
            </div>
            <div class="register-form" v-if="!successMessage">
                <div class="register-header">
                    <h3>Registration</h3>
                </div>
                <form @submit.prevent="checkForm">
                    <input v-model="login" type="text" name="login" placeholder=" Login">
                    <input v-model="password" type="text" name="password" placeholder=" Password">
                    <div v-if="errMessage != ''" >{{ errMessage }}</div>
                    <div class="register-btn"><button type="submit">Sing Up</button></div>
                </form>
            </div>
        </div> 
</template>
<script>
  import axios from 'axios'

  export default {
    data: () => ({
        login: '',
        password: '',
        errMessage: '',
        successMessage: '',
        success: false,
        config: {headers: {'Content-Type': 'application/x-www-form-urlencoded'}},
        responseAjax: {},
    }),
    created () {
    },
    methods: {
        checkForm() {
            if (this.checkLogin() && this.checkPassword()){
            
            var self = this
            self.errMessageLogIn = ""

            var data = new FormData();
            data.append('login', self.login);
            data.append('password', self.password);

            axios.post('http://rest/user1/books-shop/server/api/users/', data, self.config)
            .then(function (response) {
               self.successMessage = "Thank you for registration";
              //console.log(response);
            })
            .catch(function (error) {
              console.log(error)
            })
            }else{
                return false
            }
        },
        checkLogin(){
            if (this.login.length > 3 ){
                this.errMessage = ""
                    return true
            }else{
                this.errMessage = "Small login"
                return false;    
            } 
        },
        checkPassword(){
            if (this.password.length > 5) {
                this.errMessage = ""
                    return true
            }else{
                this.errMessage = "Password must be more than 5 symbols"
                return false;    
            }      
        }
    }
}

</script>
<style>
h3{
    color: #0000FF
}
.register-page{
margin-top: 100px;
text-align: center;
}

.register-form input{
 border: 1px solid #B0C4DE;
 padding: 5px;
 color: grey;
}

.register-btn button{
    cursor: pointer;
    margin: 15px 0 50px 0;
    background: #4169E0;
    width: 250px;
    height: 35px;
    color: #FFFFFF;
    border: none;
}

/*Options after log in*/

.user-header-btn{
    padding-top: 5px;
    text-align:center;
    font-size: 13px;
    float:left;
    width: 70px;
    cursor: pointer;
    margin: 5px 3px 5px 0;
    background: #808080;
    height: 30px;
    color: #FFFFFF;
    border: none;   
}

.user-login{
    padding-top: 5px;
    text-align:center;
    font-size: 13px;
    padding-top:10px;
    float:left;
    width: 70px;
    cursor: pointer;
    margin: 5px 3px 0 0;
    height: 30px;
    color: black;
    border: none;
    font-size: 20px;  
}

.log-options-user :first-child{
    margin-left: 250px;
}

</style>