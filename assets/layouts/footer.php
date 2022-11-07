<script src="<?php echo __LAYOUT_URL__;?>js/jquery-3.4.1.min.js"></script>
<script src="<?php echo __LAYOUT_URL__;?>js/popper.min.js"></script>
<script src="<?php echo __LAYOUT_URL__;?>js/bootstrap.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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