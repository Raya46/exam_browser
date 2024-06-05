import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import cors from 'cors';

const app = express();
app.use(cors());

const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

io.on('connection', (socket) => {
  console.log('a user connected');

  socket.on("progressUpdated", () => {
    console.log('progressUpdated event received');
    io.emit('fetch'); // Emit event to notify clients
  });
  socket.on("ujian-change", () => {
    console.log('ujian-change event received');
    io.emit('ujian-change-callback'); // Emit event to notify clients
  });
  socket.on("ujian-dikerjakan", () => {
    console.log('ujian-dikerjakan event received');
    io.emit('ujian-dikerjakan-callback'); // Emit event to notify clients
  });
  socket.on("tes", () => {
    console.log('tes event received');
    io.emit('koko', '123')
  });
  socket.on('disconnect', () => {
    console.log('user disconnected');
  });
});

server.listen(6001, () => {
  console.log('listening on *:6001');
});

export default io;
