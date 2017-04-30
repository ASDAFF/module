/**
 * AJAX веб-форма
 */
$(document).on('click', '.js-form__send-btn', function(event) {
    var $form = $(this).closest('form');
    var bHasErrors = false;

    $form.find('input:required, textarea:required').each(function() {
        this.setCustomValidity('');
        if( !this.checkValidity() ){
            this.setCustomValidity('Поле заполнено некорректно');
            bHasErrors = true;
        }
    });

    if( !bHasErrors ){
        event.preventDefault(event);
        var id = $(this).attr("id");
        var loading = new site.ui.loading('body');

        $.ajax({
            url: $form.attr('action'),
            type: 'post',
            dataType: 'json',
            data:  $form.serializeArray(),

            success: function(response) {
                if( response.hasOwnProperty('SUCCESS')
                    && response.SUCCESS
                    && response.hasOwnProperty('SUCCESS_TEXT')
                    && response.SUCCESS_TEXT.length
                ){
                    $.fancybox.open('<p class="result-text">' + response.SUCCESS_TEXT + '</p>');
                    $form.trigger('reset');
                }
                else if(response.hasOwnProperty('ERROR')
                    && response.ERROR
                    && response.hasOwnProperty('ERROR_TEXT')
                    && response.ERROR_TEXT.length){
                    $.fancybox.open('<p class="result-text">' + response.ERROR_TEXT + '</p>');
                }

                loading.hide();
                return false;
            }
        });
    }
});