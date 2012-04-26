var socket = io.connect(socketServer + '/' + site);
var productId = null;

socket.on('product', function (data) {
    var productData = JSON.parse(data);
    if (changeProduct || productData.id == productId || productId == null) {
        if (productId != productData.id) {
            $.colorbox.close();
            $.colorbox.remove();
            productId = productData.id;
            var permalink = $('#permalink');
            $('#permalink').attr('href', productData.link);
            $('#title').html(productData.title);
            permalink.appendTo('#title');
            $('#price').text('$' + productData.price);
            $('#shipping').text('+ $' + productData.shipping + ' shipping');
            $('#condition dd').text(productData.condition);
            $('#product dd').remove();
            for(var i in productData.products) {
                var product = productData.products[i];
                $('<dd>'+ product.quantity + ' ' + product.name +'</dd>').insertBefore('#product .clearfix');
            }
            $('#comment-container a').attr('href', productData.thread);
            $('#subtitle').text(productData.subtitle);
            $('#teaser').html(productData.teaser);
            $('#image img').attr('alt', productData.title.replace('"','\"'));
            $($('#image img')[0]).attr('src', '/images/products/' + productId + productData.file_extension);
            $($('#image img')[1]).attr('src', '/images/products/' + productId + '_detail' + productData.file_extension);
            $($('#image a')[0]).attr('href', '/images/products/' + productId + productData.file_extension);
            $($('#image a')[1]).attr('href', '/images/products/' + productId + '_detail' + productData.file_extension);
            $('#image a').colorbox({rel: 'productImages'});
        }
        if (productData.history[0].sold_out) {
            $('#buy-link').text('Sold out!');
        } else {
            if (parseFloat(productData.history[0].percent_sold) >= .9) {
                $('#buy-link').html('<a href="'+ productData.purchase_url +'">'
                    + "I want one!<br />(They're almost gone!)" +'</a>');
            } else {
                $('#buy-link').html('<a href="'+ productData.purchase_url +'">I want one!</a>');
            }
        }
        if (productData.wootoff) {
            var percentLeft = Math.round((1-productData.history[0].percent_sold)*100);
            if ($('#woot-off').length) {
                $('#progress-bar-inner').css('width', percentLeft +'%');
                $('#progress-percent').text(percentLeft + '%');
            } else {
                $('#product-info').after($('<div id="woot-off">Woot!-off<div id="progress-bar"><span id="progress-percent">'+ percentLeft +'%</span>'+
                    '<div id="progress-bar-inner" style="width:'+ percentLeft +'%"></div></div></div>'));
                $('#woot-off').after('&nbsp;');
            }
        } else {
            $('#woot-off').remove();
        }
        $('#comment-container a').text(
            productData.history[0].comments + ' comment' +
                ((productData.history[0].comments == 0 || productData.history[0].comments > 1) ? 's' : '')
        );
        var timeUpdated = new Date(productData.history[0].updated);
        $('#time-updated').text('last updated at ' + timeUpdated.toLocaleTimeString());
    }
});