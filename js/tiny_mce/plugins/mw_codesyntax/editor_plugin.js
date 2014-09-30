tinymce.create('tinymce.plugins.CodeSyntaxPlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'mw_codeformat':
                var mlb = cm.createListBox('mw_codeformat', {
                    title: 'Format Code',
                    onselect: function(v) {
                        var content = new String(tinyMCE.activeEditor.selection.getContent());
                        content = content.replace(/<(p)([^>]*)>/g, '');
                        content = content.replace(/<\/(p)>/g, '');
                        //content = content.replace(/\/(&nbsp;)/g, '\n\r');
                        content = content.replace(/<br\s*[\/]?>/gi, '\n');

                        tinyMCE.activeEditor.selection.setContent('<pre class="brush:' + v + ';">' + content + '</pre>');
                    }
                });
           
                // Add some values to the list box
                mlb.add('Applescript', 'applescript');
                mlb.add('Actionscript3 As3', 'actionscript3 as3');
                mlb.add('Bash Shell', 'bash shell');
                mlb.add('Coldfusion Cf', 'coldfusion cf');
                mlb.add('Cpp C', 'cpp c');
                mlb.add('C# C-sharp Csharp', 'c# c-sharp csharp');
                mlb.add('CSS', 'css');
                mlb.add('Delphi Pascal', 'delphi pascal');
                mlb.add('Diff Patch Pas', 'diff patch pas');
                mlb.add('Erl Erlang', 'erl erlang');
                mlb.add('Groovy', 'groovy');
                mlb.add('Java', 'java');
                mlb.add('Jfx Javafx', 'jfx javafx');
                mlb.add('Js Jscript Javascript', 'js jscript javascript');
                mlb.add('Perl Pl', 'perl pl');
                mlb.add('Php', 'php');
                mlb.add('Text Plain', 'text plain');
                mlb.add('Py Python', 'py python');
                mlb.add('Ruby Rails Ror Rb', 'ruby rails ror rb');
                mlb.add('Sass Scss', 'sass scss');
                mlb.add('Scala', 'scala');
                mlb.add('SQL', 'sql');
                mlb.add('Vb Vbnet', 'vb vbnet');
                mlb.add('Xml Xhtml Xslt Html', 'xml xhtml xslt html');
   
                // Return the new listbox instance
                return mlb;
        }

        return null;
    }/*,

    getInfo: function() {
        return {
            longname: 'Code Syntax Plugin',
            author: 'Jacobus Meintjes',
            authorurl: 'http://www.phoenixcode.net',
            infourl: 'http://alexgorbatchev.com/wiki/SyntaxHighlighter',
            version: '1.0'
        };
    }*/
});

    // Register plugin with a short name
    tinymce.PluginManager.add('mw_codesyntax', tinymce.plugins.CodeSyntaxPlugin);