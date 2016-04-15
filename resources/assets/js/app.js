$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Enable pusher logging - don't include this in production
Pusher.log = function (message) {
    if (window.console && window.console.log) {
        window.console.log(message);
    }
};

var pusher = new Pusher('3c511981ee118763016d', {
    encrypted: true
});

window.lock = false;

var chat = pusher.subscribe('chat');

chat.bind('App\\Events\\FirstCommitment', function (data) {
    if (data.client_id !== window.client_id) {
        var message = '<p><i>' +
            'Received R1 and hash!' +
            '<br>R1 = ' + data.r1 +
            '<br>Hash = ' + data.hash +
            '</i></p>';

        $('#messages').append($(message).hide().fadeIn(500));

        $.post('commitment/first', data).done(function (data) {

        }).fail(function (data) {
            console.log('Something went wrong', data);
        });
    }
});

chat.bind('App\\Events\\SecondCommitment', function (data) {
    if (data.client_id !== window.client_id) {
        var message = '<p><i>' +
            'Received message, R1 and R2!' +
            '<br>R1 = ' + data.r1 +
            '<br>R2 = ' + data.r2 +
            '</i></p>';

        $('#messages').append($(message).hide().fadeIn(500));

        $.post('commitment/second', data).done(function (data) {
            $('#messages').append($(data.response).hide().fadeIn(500));
        }).fail(function (data) {
            console.log('Something went wrong', data);
        });
    }

    else if(data.client_id === window.client_id) {
        $('#messages').append($('<p><i>Sent R1, R2 and message</i></p>').hide().fadeIn(500));
    }
});

$('#sendMessage').submit(function (e) {
    e.preventDefault();
    var vm = $(this);
    var errors = $('.errors');
    errors.hide().text('');

    if (window.lock === false) {
        window.lock = true;
        $.post('chat/new', vm.serialize()).done(function (data) {
            vm.find('textarea').val('');
            var message = '<p>[' + data.created_at + ']: ' + data.content + '</p>' +
                '<p><i>Using commitment scheme with...' +
                '<br>R1: ' + data.r1 +
                '<br>R2: ' + data.r2 +
                '<br>Hash: ' + data.hash +
                '<br>Message: ' + data.content + '</i></p>' +
                '<p><i>Sent hashed message and R2</i></p>';
            $('#messages').append($(message).hide().fadeIn(500));
            window.lock = false;
        }).fail(function (data) {
            window.lock = false;
            var response = $.parseJSON(data.responseText);
            errors.show();
            $.each(response, function (key, value) {
                errors.append('<p>' + value + '</p>');
            });
        });
    }

    return false;
});

$('textarea').keypress(function (e) {
    if (e.which == 13) {
        $(this).closest('form').submit();
        return false;
    }
});