/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,BidiLtr,BidiRtl,Language,Flash,Iframe,About,HiddenField,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,CreateDiv,Anchor';
        
        config.extraPlugins = 'imageuploader,youtube';
        //config.filebrowserBrowseUrl = 'http://localhost:8081/CodeIgniter/Midias/Imagebrowser';
        //mudar esse path em ckeditor/plugins/imageuploader/plugin.js
        
    /*config.toolbar = 'ToolbarSimples';
 
	config.toolbar_ToolbarSimples =
	[
		{ name: 'styles',       items   :   [ 'Styles','Format' ] },
		{ name: 'basicstyles',  items   :   [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
		{ name: 'paragraph',    items   :   [ 'NumberedList','BulletedList','-','Outdent','Indent','-', 'List', 'Blocks', 'Align', 'Bidi' ] },
                { name: 'document',     items   :   [ 'NewPage','Preview' ] },
		{ name: 'clipboard',    items   :   [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'insert',       items   :   [ 'Table','HorizontalRule','Smiley','SpecialChar' ] }
	];*/
};