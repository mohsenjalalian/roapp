/**
 * Created by mohsenjalalian on 11/8/16.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var r = require("rethinkdb");

app.get('/', function(req, res){
    res.sendFile(__dirname + '/index.html');
});

io.on('connection', function(socket){

    r.connect({host: 'localhost', port: 28015}).then(function(conn) {
        r.db('roapp').table('driver_location').pluck('lat', 'lng').changes()
            .run(conn, function (err, result) {
                if (err) throw err;
            })
            .then(function (cursor) {
                cursor.each(function(err, data) {
                    io.emit('chat message', JSON.stringify(data));
                })
            })
    });
});

http.listen(4000, function(){
    console.log('listening on *:4000');
});