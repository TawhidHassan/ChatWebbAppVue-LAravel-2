<template>
    <div class="container-fluid clearfix" id="chat">
        <div class="row offset-2">
            <div class="people-list bg-info" id="people-list">
                <div class="search">
                    <input type="text" placeholder="search" class="text-black"/>
                    <i class="fa fa-search"></i>
                </div>
                <ul class="list">
                    <li class="clearfix" v-for="user in userlist" :key="user.id" @click.prevent="selectuser(user.id)">
                        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />
                        <div class="about">
                            <div class="name">{{user.name}}</div>
                            <div class="status"  style="color:#fff">
                                <div  v-if="onlineUser(user.id) || online.id==user.id" >
                                    <i class="fa fa-circle online"></i> online
                                </div>
                                <div v-else>
                                    <i  class="fa fa-circle"></i> offline
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="chat">
                <div class="chat-header clearfix"  v-if="usermassage.user">
                    <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar" />

                    <div class="chat-about"  v-if="usermassage.user">
                        <div class="chat-with" v-if="usermassage.user">{{usermassage.user.name}}</div>
                        <div class="chat-num-messages">already 1 902 messages</div>
                    </div>
                    <i class="fa fa-star"></i><ul class="nav nav-tabs">
                        <!--delete all massage-->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">...</a>
                        <div class="dropdown-menu">
                            <a @click.prevent="deleteAllMessage" class="dropdown-item" href="#">Delete All Message</a>
                        </div>
                    </li>

                </ul>
                </div> <!-- end chat-header -->

                <div class="chat-history" v-chat-scroll="{always: false, smooth: true}">
                    <ul>
                        <li class="clearfix" v-for="massage in usermassage.massage" :key="massage.id">
                            <div :class="`message-data  ${massage.user.id==usermassage.user.id ? '':'align-right'}`">
                                <span class="message-data-time" >{{massage.created_at|timeformet}}</span> &nbsp; &nbsp;
                                <span class="message-data-name" >{{massage.user.name}}</span> <i class="fa fa-circle me"></i>
                                <ul class="nav nav-tabs">

                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">...</a>
                                        <div class="dropdown-menu">
                                            <a @click.prevent="deletemassage(massage.id)" class="dropdown-item" href="#">Delete Message</a>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                            <div :class="`message  float-right ${massage.user.id==usermassage.user.id ? 'my-message':'other-message'}`">
                               {{massage.message}}
                            </div>
                        </li>


                    </ul>

                </div> <!-- end chat-history -->

                <div class="chat-message bg-info clearfix">
                    <p v-if="typing">{{typing}} typing.......</p>
                    <textarea v-if="usermassage.user" @keyup="typeingEvent(usermassage.user.id)" @keydown.enter="sendmassage" v-model="message" name="message" id="message-to-send" placeholder ="Type your message" rows="3" class="bg-success"></textarea>
                    <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
                    <i class="fa fa-file-image-o"></i>

                    <button>Send</button>

                </div> <!-- end chat-message -->

            </div> <!-- end chat -->
        </div>


    </div> <!-- end container -->
</template>

<script>
    import _ from 'lodash'
    export default {
        name: "ChatApp",
        data()
        {
            return{
                message:'',
                typing:'',
                online:'',
                users:[],
            }
        },
        mounted()
        {
            Echo.private(`chat.${authuser.id}`)
                .listen('MessageSend', (e) => {
                    this.selectuser(e.message.from);
                    // console.log(e.message.message);
                });
            this.$store.dispatch('userlist')
            Echo.private('typingevent')
                .listenForWhisper('typing', (e) => {
                    if(e.user.id==this.usermassage.user.id && e.userId == authuser.id){
                        this.typing = e.user.name;
                    }
                    setTimeout(() => {
                        this.typing = '';
                    }, 2000);
                });

            Echo.join('liveuser')
                .here((users) => {
                    this.users = users
                })
                .joining((user) => {
                    this.online = user
                })
                .leaving((user) => {
                    console.log(user.name);
                });
        },
        created()
        {

        },
        computed:{
            userlist()
            {
                return this.$store.getters.userlist
            },
            usermassage()
            {
                return this.$store.getters.usermassage
            },
        },
        methods:{
            selectuser(userid)
            {
                this.$store.dispatch('usermassage',userid)
            },
            sendmassage(e)
            {
                e.preventDefault()
                if (this.message!=='')
                {
                    axios.post('/sendmassage',{message:this.message,user_id:this.usermassage.user.id})
                        .then(response=>{
                           this.selectuser(this.usermassage.user.id)
                        })
                    this.message = '';
                }
            },
            deletemassage(messageId){
                axios.get('/deletesinglemessage/'+messageId)
                    .then(response=>{
                        this.selectuser(this.usermassage.user.id)
                    })
            },
            deleteAllMessage(){
                axios.get(`/deleteallmessage/${this.usermassage.user.id}`)
                    .then(response=>{
                        this.selectuser(this.usermassage.user.id)
                    })
            },
            typeingEvent(userId){
                Echo.private('typingevent')
                    .whisper('typing', {
                        'user': authuser,
                        'typing':this.message,
                        'userId':userId
                    });
            },
            onlineUser(userId){
                return _.find(this.users,{'id':userId});
            },
        },
        watch:{

        }

    }
</script>

<style scoped>

</style>
