var socket = io.connect(socketServer + '/' + site);
var productId;

socket.on('product', function (data) {
    var productData = JSON.parse(data);

    if (productId != productData.id) {
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
        $('#teaser').text(productData.teaser);
        $('#image img').attr('alt', productData.title.replace('"','\"'));
        $('#image img').attr('src', '/images/products/' + productId + productData.file_extension);
    }
    if (productData.history[0].sold_out) {
        $('#buy-link').text('Sold out!');
    } else {
        if (parseFloat(productData.history[0].percent_sold) >= .9) {
            $('#buy-link').html('<a href="'+ productData.purchase_url +'">' + "I want one!<br />(They're almost gone!)" +'</a>');
        } else {
            $('#buy-link').html('<a href="'+ productData.purchase_url +'">I want one!</a>');
        }
    }
    $('#comment-container a').text(
        productData.history[0].comments + ' comment' +
            ((productData.history[0].comments == 0 || productData.history[0].comments > 1) ? 's' : '')
    );
    var timeUpdated = new Date(productData.history[0].updated);
    $('#time-updated').text('last updated at ' + timeUpdated.toLocaleTimeString());
});