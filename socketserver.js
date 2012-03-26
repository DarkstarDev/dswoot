/**
 * @var siteUrl string
 * @example subdomain.domain.tld
 */
var siteUrl = "not.configured.tld";
var io = require('socket.io').listen(8080),
    http = require('http');

//All connections to be used
var connections = {
    woot: io.of('/woot')
        .on('connection', function (socket) {
            socket.join('woot');
        }
    ),

    home: io.of('/home')
        .on('connection', function (socket) {
            socket.join('home');
        }
    ),

    kids: io.of('/kids')
        .on('connection', function (socket) {
            socket.join('kids');
        }
    ),

    shirt: io.of('/shirt')
        .on('connection', function (socket) {
            socket.join('shirt');
        }
    ),

    sellout: io.of('/sellout')
        .on('connection', function (socket) {
            socket.join('sellout');
        }
    ),

    wine: io.of('/wine')
        .on('connection', function (socket) {
            socket.join('wine');
        }
    ),

    moofi: io.of('/moofi')
        .on('connection', function (socket) {
            socket.join('moofi');
        }
    )
}

//Mimic an AJAX request
var XMLHttpRequestHeader = {
    'X-Requested-With': 'XMLHttpRequest'
};

//Request options
var options = {
    woot: {
        host: siteUrl,
        port: 80,
        path: '/woot',
        headers: XMLHttpRequestHeader
    },
    kids: {
        host: siteUrl,
        port: 80,
        path: '/kids',
        headers: XMLHttpRequestHeader
    },
    home: {
        host: siteUrl,
        port: 80,
        path: '/home',
        headers: XMLHttpRequestHeader
    },
    shirt: {
        host: siteUrl,
        port: 80,
        path: '/shirt',
        headers: XMLHttpRequestHeader
    },
    sellout: {
        host: siteUrl,
        port: 80,
        path: '/sellout',
        headers: XMLHttpRequestHeader
    },
    wine: {
        host: siteUrl,
        port: 80,
        path: '/wine',
        headers: XMLHttpRequestHeader
    },
    moofi: {
        host: siteUrl,
        port: 80,
        path: '/moofi',
        headers: XMLHttpRequestHeader
    }
};

//Fetch data every 30 seconds for all sites
var siteData = {};
setInterval((function() {
    for (var i in options) {
        var fetch = function(site, data) {
            var req = http.get(options[site], function(res) {
                res.setEncoding('utf8');
                res.on('data', function(chunk) {
                    //If data is different, send it
                    if (data[site] !== chunk) {
                        data[site] = chunk;
                        connections[site].emit('product', data[site]);
                    }
                });
            });

            req.on('error', function(e) {
                console.log('ERROR: ' + JSON.stringify(e));
            });
        }(i, siteData);
    }
    }),
    30000
);
