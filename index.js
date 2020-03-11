var app = require('express')();
var http = require('http').createServer(app);
var io = require('socket.io')(http);

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

io.on('connection', function(socket){
  console.log('new user connected 1');
    socket.on('tnp', function(msg){
        io.emit('tnp', msg);
    });
});

setInterval( ()=>{
    io.emit('chatmessage', "KEK");
}, 2000);

http.listen(3000, function(){
  console.log('listening on *:3000');
});
