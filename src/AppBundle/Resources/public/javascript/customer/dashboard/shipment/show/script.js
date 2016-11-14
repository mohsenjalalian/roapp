/**
 * Created by mohsenjalalian on 11/13/16.
 */
var socket = io('http://localhost:4000');

socket.on('chat message', function(msg){
    $('.table').append(msg);
});