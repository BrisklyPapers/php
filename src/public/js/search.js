$(window).load(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function nl2br (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    var wto;

    $('#search').keypress(function () {
        $('#upload_area').hide();

        clearTimeout(wto);
        wto = setTimeout(function() {

            $.ajax({
                url: '/document/search',
                data: {q: $('#search').val()},
                type: "get",
                success: function (jqXHR, textStatus) {
                    $('#response').empty();

                    $.each(jqXHR, function(i, file) {
                        $link = $('<a>', {
                            title: file.fileName,
                            text: file.fileName,
                            href: file.url,
                            target: "_blank"
                        });
                        $div = $('<div>', {
                            class: 'search_results'
                        });
                        $div.append($link).append($('<br/>')).append(file.text);

                        $('#response').prepend($div);
                    });
                },
                error: function (jqXHR, textStatus) {
                    document.getElementById("response").innerHTML = textStatus + "<br/><pre>" + nl2br(jqXHR.responseText) + "</pre>";
                }
            });
        }, 50);
    });
});