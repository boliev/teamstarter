var io = require('socket.io')(8081);

io.on('connection', function (socket) {
    io.emit('this', { will: 'be received by everyone'});

    socket.on('room', function(room) {
        socket.join(room);
    });

    socket.on('message', function (data) {
        io.to(data.offer).emit('message', data);
    });

    socket.on('disconnect', function () {
        io.emit('user disconnected');
    });
});