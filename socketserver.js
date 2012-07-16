/**
 * Set this to the FQDN your site lives at
 * @var siteHost string
 */
var siteHost = "not.configured.tld";

/**
 * The port your site listens on
 * @var sitePort int
 */
var sitePort = 80;

/**
 * The path to the woot-checker if it resides in a subdirectory
 * of the site.  Leave empty if this does not apply.
 * @var sitePath string
 */
var sitePath = '';

/**
 * The port socket.io will listen on.  If you change this you must
 * also change it in application/configs/application.ini.
 * @var socketIOPort int
 */
var socketIOPort = 8080;


/**
 * You shouldn't have to modify anything below this line.
 * If you do, you're on your own.
 */
var io = require('socket.io').listen(socketIOPort),
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
    ),

    sport: io.of('/sport')
        .on('connection', function (socket) {
            socket.join('sport');
        }
    ),

    tech: io.of('/tech')
        .on('connection', function (socket) {
            socket.join('tech');
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
        host: siteHost,
        port: sitePort,
        path: sitePath + '/woot',
        headers: XMLHttpRequestHeader
    },
    kids: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/kids',
        headers: XMLHttpRequestHeader
    },
    home: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/home',
        headers: XMLHttpRequestHeader
    },
    shirt: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/shirt',
        headers: XMLHttpRequestHeader
    },
    sellout: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/sellout',
        headers: XMLHttpRequestHeader
    },
    wine: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/wine',
        headers: XMLHttpRequestHeader
    },
    moofi: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/moofi',
        headers: XMLHttpRequestHeader
    },
    sport: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/sport',
        headers: XMLHttpRequestHeader
    },
    tech: {
        host: siteHost,
        port: sitePort,
        path: sitePath + '/tech',
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
