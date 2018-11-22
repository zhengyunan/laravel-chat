<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
     <div id="app">
       <ul>
          <li v-for = "v in messages">@{{v.content}}</li>
       </ul>
       <textarea v-model="content"></textarea>
       <input @click="send" type="button" value="发送消息">
     </div>
</body>
</html>
<script src="/vue.js"></script>
<script src="/axios.min.js"></script>
<script>
    new Vue({
        el:'#app',
        data:{
            ws:null,
            messages:[],
            content:''
        },
        // 初始化自动调用
        created:function(){
            axios.get('/api/messages').then((res)=>{
                this.messages = res.data
            })
           this.ws = new WebSocket('ws://127.0.0.1:8282')
           this.onmessage = this.message
        },
        methods:{
            // 收消息
            message:function(e){
               this.messages.push({content:e.data})
            },
            // 发消息
            send:function(){
                if(this.content!=''){
                    var params = new URLSearchParams()
                    params.append('content', this.content)
                    axios.post('/api/messages', params)
                    this.content = '' 
                }
            }
        }
    })
</script>