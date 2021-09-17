'use strict';

var http = require('http'),
  md5 = require('md5');

module.exports = function(PF) {
  // phpFox hosting
  if (PF.config.is_hosted) {
    PF.event.on('socket_connection', function(params) {
      var host = params.host;
      if (params.token !== '') {
        console.log(params.token);
        params.redis.get('im/host/access/token/' + params.token,
          function(err, result) {
            console.log('GET TOKEN FROM REDIS: ', result ? 'ok' : 'failed')
            if (result) {
              // verify client succesfully
              params.socket.on('disconnect', function() {
                PF.connection[host] = PF.connection[host] - 1;
              });

              // check available connections
              if (PF.connection.hasOwnProperty(host)) {
                PF.connection[host] = PF.connection[host] + 1;
                // exceed max connections
                if (PF.connection[host] > PF.max_connection) {
                  PF.connectFailed(socket, 'phpFox IM hosting is overloaded. Please try again later.');
                }
                else {
                  PF.connectSuccesfully(params.socket);
                }
              }
              else {
                PF.connection[host] = 1;
                PF.connectSuccesfully(params.socket);
              }
            }
            else {
              // verify client failed
              PF.connectFailed(params.socket, undefined, false);
            }
          });
      } else {
        // support old client IM 4.5.3, verify via emit('host')
        console.log('VERIFY OLD CLIENT');
        params.socket.on('host', function (token) {
          params.redis.get('im/host/access/token/' + token.token, function (err, result) {
            console.log('GET TOKEN FROM REDIS: ', result ? 'ok' : 'failed');
            if (result) {
              params.redis.del('im:host:failed:' + host);
              PF.connectSuccesfully(params.socket);
            } else {
              params.redis.set(['im:host:failed:' + host, 1]);
              PF.connectFailed(params.socket, undefined, false);
            }
          });
        });
      }
    });
  }
  else {
    // self hosting
    if (PF.config.chat_server_key !== '') {
      PF.event.on('socket_connection', function(params) {
        var date = new Date();
        date.setHours(0, 0, 0, 0);
        var timestamp = (date.valueOf() - date.getTimezoneOffset() *
          60000) / 1000,
          server_token = md5(timestamp + PF.config.chat_server_key);

        console.log('VERIFY TOKEN');
        console.log('CLIENT TOKEN:', params.token);
        console.log('SERVER TOKEN:', server_token);
        if (params.token === server_token) {
          PF.connectSuccesfully(params.socket);
        }
        else {
          PF.connectFailed(params.socket, undefined, true);
        }
      });
    } else {
      if (!PF.config.is_hosted) {
        console.log('chat_server_key is missing.');
        process.exit(1);
      }
    }
  }
};