<?php
$jscript = "
if(typeof ckeditor_initialize != 'function'){
    function ckeditor_initialize(sid, mode){
        return function(){
            var ele  = $('#' + sid);
            if(!ele.length) return;
            if(ele.data('ckeditor_built')) return;
            ele.attr('data-ckeditor_built',true);
            CKEDITOR.config.contentsLangDirection = document.dir;
            CKEDITOR.config.extraAllowedContent = 'img(parsed_image)';
            if(mode == 'full_height'){
                CKEDITOR.config.height = window.innerHeight-110;
                CKEDITOR.replace(sid, {
                    on: {
                        getData: function( evt ) {
                            evt.data.dataValue = '<div class=\"core-ckeditor-content\">' + evt.data.dataValue + '</div>';
                    }
                }});
            }else if(mode == 'simple'){
                CKEDITOR.config.height = 60;
                CKEDITOR.replace(sid, {
                    toolbar: [{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] }],
                    on: {
                        getData: function( evt ) {
                            evt.data.dataValue = '<div class=\"core-ckeditor-content\">' + evt.data.dataValue + '</div>';
                        }
                    }
                });
            }else{
                CKEDITOR.replace(sid,{
                    removePlugins: 'sourcearea',
                    on: {
                        getData: function( evt ) {
                            evt.data.dataValue = '<div class=\"core-ckeditor-content\">' + evt.data.dataValue + '</div>';
                        }
                    }
                });
            }
        }
    }
}";

if (!isset($aParams['enter'])) {
    $mode = isset($aParams['mode']) && $aParams['mode'] ? $aParams['mode'] : (isset($aParams['simple']) && $aParams['simple'] ? 'simple' : 'none');
    $sStr .= '<script>' . $jscript . '$Behavior.loadEditor' . $iId . '  = ckeditor_initialize("' . $iId . '","' . $mode . '");' . '</script>';
}
