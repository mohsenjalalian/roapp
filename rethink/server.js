/**
 * Created by mohsenjalalian on 11/9/16.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var r = require("rethinkdb");

app.get('/', function(req, res){
    res.sendfile('client.html');
});

io.on('connection', function(socket){
    socket.on('data', function(data){
        r.connect({host: 'localhost', port: 28015}).then(function(conn) {
            var currentTime = new Date();
            r.db('roapp').table('driver_location').insert(
                [
                    {
                        lat: data.lat,
                        lng: data.lng,
                        driver_token: data.driverToken,
                        DateTime: currentTime
                    }
                ]
            ).run(conn, function (err, result) {
                    if (err) throw err;
                    console.log(JSON.stringify(result, null, 2));
                })
        });
    });
});

http.listen(3000, function(){
    console.log('listening on localhost:3000');
});