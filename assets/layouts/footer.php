<script src="<?php echo __LAYOUT_URL__;?>js/jquery-3.4.1.min.js"></script>
<script src="<?php echo __LAYOUT_URL__;?>js/popper.min.js"></script>
<script src="<?php echo __LAYOUT_URL__;?>js/bootstrap.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script type="text/javascript">


const writeLog = function(msg) {
        let date = new Date();
        window.console.log(date.toISOString() + ' ' + msg);
    };
    jQuery(function($) {
        let f = $('#formId');

       

        let xhrOptions = {
            url: f.attr('action'),
            type: f.attr('method'),
            data: {},
            cache: false,
            xhr: function() {
                let xhr = $.ajaxSettings.xhr();
                if ('undefined' === typeof xhr.upload.onload || null === xhr.upload.onload) {
                    //override the upload.onload event, only if it has not already been
                    xhr.upload.onload = function() {
                        //onload is triggered immediately after the POST headers are sent to the recipient
                        writeLog('Upload Completed - Ending Request');
                        xhr.abort();
                    };
                }
                return xhr;
            },
        };
        f.on('submit', function(e) {
            e.preventDefault();

            let redirect_url = document.getElementById('redirect').value;
            writeLog(redirect_url);

            let formData = f.serialize();
            writeLog(formData);
            $.ajax($.extend(true, xhrOptions, {
                data: formData
            })).done(function(responseText) {
                //this is never triggered since the request is aborted
                writeLog('Success');
                writeLog(responseText);
            }).fail(function(jqxhr) {
                writeLog('Request ' + (jqxhr.readyState !== 4 ? 'Aborted' : 'Failed'));

                location.href=redirect_url;
            });

            
            
            
           // 

            return false;
        });
    });
    
   </script>
<?php
if(isset($json_array))
{
	?>

<script type="text/javascript">
    $(window)
            .load(
                    function() {
                        var JSON = <?php echo json_encode($json_array); ?>
						                        
						$(function() {

                            function parseMenu(ul, menu) {
                                for (var i = 0; i < menu.length; i++) {
                                    var li = $(ul).append(
                                            '<li class='+(menu[i].sub?'multi':'simple')+'><a href="'+ menu[i].link+'">'+ menu[i].name
                                                    + '</a></li>');
                                    if (menu[i].sub != null) {
                                        var subul = $('<ul class="list"></ul>');
                                        $(li).append(subul);
                                        parseMenu($(subul), menu[i].sub);
                                            }
                                }
                            }

                            var menu = $('#menu');
                            parseMenu(menu, JSON.menu);
                        });
                    });//]]>​
</script>
<script type="text/javascript">
$(document).on('click', '.list > li ', function () {
    $(this).next('ul').toggle(200);
    if(($(this).next('ul').length)){
      $(this).toggleClass('multi-opened');
    } 
})</script>
<?php
}
	?>
</body>
</html>