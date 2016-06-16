/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//換行模組
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;

	/*外接css檔*/
 	config.contentsCss = '/css/editor.css';
 	config.allowedContent = true;
 	config.extraPlugins = 'customStyle,videodetector';
 	config.toolbar =
 		[
	 		
	 		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	 		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	 		{ name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
	 		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	 		
	 		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat','customStyle' ] },
	 		'/',
	 		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
	 		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
	 		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','Source' ] },
	 		{ name: 'insert', items : [ 'Image' ,'VideoDetector'] },
	 	]
 	
};
