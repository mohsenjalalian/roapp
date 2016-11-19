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
        r.connect({host: 'localhost', port: 28015, password: '09126354397'}).then(function(conn) {
            r.db('roapp').table('shipment').filter(r.row('driver_token').eq(data.driverToken)
            ).run(conn, function (err, cursor) {
                if (err) throw err;
                cursor.toArray(function(err, result) {
                    if (err) throw err;
                    if (result[0] !== 'undefined' && typeof result[0] !== 'undefined') {
                        id = result[0].id;
                        var currentTime = new Date();
                        r.db('roapp').table('driver_location').insert(
                            [
                                {
                                    lat: data.lat,
                                    lng: data.lng,
                                    date_time: currentTime,
                                    shipment_id: id
                                }
                            ]
                        ).run(conn, function (err, result) {
                            if (err) throw err;
                        })
                    } else {
                        socket.disconnect();
                    }
                });
            });
        });
    });
});

http.listen(3000, function(){
    console.log('listening on localhost:3000');
});