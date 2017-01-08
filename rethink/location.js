/**
 * Created by mohsenjalalian on 1/3/17.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var r = require("rethinkdb");

io.on('connection', function(socket){
    socket.on('data', function(data) {
        r.connect({host: 'localhost', port: 28015, password: '09126354397'}).then(function (conn) {
            r.db('roapp').table('driver').filter(
                r.row('tracking_token').eq(data)
            ).run(conn, function (err, cursor) {
                if (err) throw err;
                cursor.toArray(function(err, result) {
                    if (err) throw err;
                    var id = result[0].id;
                    var tracking_token = result[0].tracking_token;
                    if (result[0] !== 'undefined' && typeof result[0] !== 'undefined') {
                        r.db('roapp').table('location').filter(r.row('driver_id').eq(id)).pluck('lat', 'lng').changes()
                            .run(conn, function (err, result) {
                                if (err) throw err;
                            })
                            .then(function (cursor) {
                                cursor.each(function (err, data) {
                                    var location = JSON.stringify(data);
                                    var output = {
                                        "location" : location,
                                        "tracking_token" : tracking_token
                                    };
                                    io.emit('chat message', output);
                                })
                            })
                    } else {
                        socket.disconnect();
                    }
                })
            });
        });
    })
});

http.listen(4500, function(){
    console.log('listening on *:4500');
});