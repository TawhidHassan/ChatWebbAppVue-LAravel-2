export default {
    state: {
        user:[],
        usermassage:[],
    },
    mutations: {
        userlist(state,data)
        {
            state.user=data
        },
        usermassage(state,data)
        {
            state.usermassage=data
        }
    },
    actions: {
        userlist(context)
        {
            axios.get('/getuser')
                .then(response=>{
                    context.commit("userlist",response.data);
                })
        },
        usermassage(context,payload)
        {
            axios.get('/getusermassage/'+payload)
                .then(response=>{
                    context.commit("usermassage",response.data);
                })
        }
    },
    getters: {
        userlist(state)
        {
            return state.user
        },
        usermassage(state)
        {
            return state.usermassage
        }
    },

}
