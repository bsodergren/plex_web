    var $V__EDITABLE__V = $('.V__EDITABLE__V');
    
            function V__FUNCTION__V() {
                $V__EDITABLE__V.editable({
                    emptyMessage : 'Please write something...',
                    callback : function( data ) {
                        console.log('Stopped editing ' + data.$el[0].nodeName );
                        if( data.content )
                        {
                            $.ajax({
                                type: 'post',
                                url: 'process.php',                        
                                data: jQuery.param({'submit': 'update', V__ID_NAME__V: data.content,'video_key': 'V__VIDEO_KEY__V'}) ,
                            });
                            console.log('   * The text was changed -> ' + data.content);
                        }
                    }
                });
            };
    
            // Listen on when elements getting edited
            $V__EDITABLE__V.on('edit', function( $textArea ) {
 
                console.log('Started editing element '+ this.nodeName);
            });
    
    
            V__FUNCTION__V();