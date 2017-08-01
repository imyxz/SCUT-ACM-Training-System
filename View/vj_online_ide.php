<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>
<div class="row">
    <div class="l12">
        <div id="editor" ></div>
    </div>
</div>

    <script>
        var editor;
        $(document).ready(function(){
            ace.require("ace/ext/language_tools");
            editor = ace.edit("editor");
            editor.getSession().setMode("ace/mode/c_cpp");
            editor.setTheme("ace/theme/twilight");
            editor.setFontSize(16);
            editor.setOptions({
                enableBasicAutocompletion: true,
                enableSnippets: true,
                enableLiveAutocompletion: true
            });
        });
    </script>
<?php include('footer.php');?>