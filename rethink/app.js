/**
 * Created by mohsenjalalian on 11/8/16.
 */
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var r = require("rethinkdb");

io.on('connection', function(socket){
    socket.on('data', function(data) {
        r.connect({host: 'localhost', port: 28015, password: '09126354397'}).then(function (conn) {
            r.db('roapp').table('shipment').filter(
                r.row('tracking_token').eq(data).and(r.row('status').eq('enabled'))
            ).run(conn, function (err, cursor) {
                if (err) throw err;
                cursor.toArray(function(err, result) {
                    if (err) throw err;
                    var id = result[0].id;
                    if (result[0] !== 'undefined' && typeof result[0] !== 'undefined') {
                        r.db('roapp').table('driver_location').filter(r.row('shipment_id').eq(id)).pluck('lat', 'lng').changes()
                            .run(conn, function (err, result) {
                                if (err) throw err;
                            })
                            .then(function (cursor) {
                                cursor.each(function (err, data) {
                                    io.emit('chat message', JSON.stringify(data));
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

http.listen(4000, function(){
    console.log('listening on *:4000');
});