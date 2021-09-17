var fs = require('fs'),
    data = JSON.parse(fs.readFileSync('im-data.json')),
    config = require('./config.js'),
    redis_lib = require('redis'),
    redis = redis_lib.createClient(config.redis),
    kue = require('kue'),
    queue = kue.createQueue();

queue.create('im_import_threads', data.thread).save();
queue.create('im_import_message', data.message).save();
queue.create('im_import_user_threads', data.threads).save();

queue.process('im_import_threads', function (job, done) {
    for (var i in job.data) {
        import_threads_closure(i, job.data, job.data.length, done)();
    }
});
function import_threads_closure (i, data, length, done) {
    return function () {
        // check thread exist?
        redis.get('thread:' + data[i].thread_id, function (err, thread) {
            if (thread === null) {
                // check revert thread exist?
                var revert_thread = (((data[i].thread_id).split(':')).reverse()).join(':');
                redis.get('thread:' + revert_thread, function (err, thread_result) {
                    if (thread_result === null) {
                        redis.set(['thread:' + data[i].thread_id, JSON.stringify(data[i])]);
                    }
                    if (i == length - 1) {
                        done();
                    }
                });
            }
            if (i == length - 1) {
                done();
            }
        });
    }
}

// import messages
queue.process('im_import_message', function (job, done) {
    for (var i in job.data) {
        var users_id = (job.data[i].thread_id).split(':');
        import_message_closure(users_id, i, job, done)();
    }
});

function import_message_closure (users_id, i, job, done) {
    return function () {
        redis.lrange(['threads:' + users_id[0], 0, -1], function (err, thread) {
            var thread_revert = users_id.reverse().join(':');
            if (thread.indexOf(thread_revert) === -1) {
                redis.zadd(['message:' + job.data[i].thread_id + '', job.data[i].time_stamp, JSON.stringify(job.data[i])]);
            } else {
                redis.zadd(['message:' + thread_revert + '', job.data[i].time_stamp, JSON.stringify(job.data[i]).replace(job.data[i].thread_id, thread_revert)]);
            }
            if (i == job.data.length - 1) {
                done();
            }
        });
    }
}

queue.process('im_import_user_threads', function (job, done) {
    var length = 0;
    for( var key in job.data ) {
        if( job.data.hasOwnProperty(key) ) {
            ++length;
        }
    }
    var icount = 0;
    for (var i in job.data) {
        icount++;
        var jcount= 0;
        for (var j in job.data[i]) {
            jcount++;
            import_user_threads_closure(i, j, job.data, icount, length, jcount, job.data[i].length, done)();
        }
    }
});

function import_user_threads_closure (i, j, data, icount, ilength, jcount, jlength, done) {
    return function () {
        redis.lrange(['threads:' + i, 0, -1], function (error, threads) {
            var thread_revert = ((data[i][j].split(':')).reverse()).join(':');
            if (typeof threads === 'undefined' || threads.length === 0) {
                redis.lpush(['threads:' + i, data[i][j]]);
            } else if (threads.indexOf(data[i][j]) === -1 && threads.indexOf(thread_revert) === -1) {
                redis.lpush(['threads:' + i, data[i][j]]);
            }
            if (icount == ilength && jcount == jlength) {
                done();
            }
        });
    }
}

queue.on( 'error', function( err ) {
    console.log( 'Oops... ', err );
});

var job_count = 0;
queue.on( 'job complete', function( id, result ) {
    kue.Job.get(id, function(err, job){
        if (err) return;
        job.remove(function(err){
            if (err) throw err;
            job_count++;
            if (job_count == 3) {
                queue.shutdown(function () {
                    console.log('=============================');
                    console.log('Import IM data successfully.');
                    process.exit(0);
                })
            }
        });
    });
});