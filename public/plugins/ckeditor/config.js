/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.defaultLanguage = 'en-gb';
	// config.uiColor = '#AADC6E';
	
	
	
	if(isMobile)
	{
		config.toolbarGroups = [
		
		
		'/',
		
		/*{ name: 'tliyoutube' },*/
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		/*{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' },*/
		
 		 
 		];	
	}
	else
	{
		
		config.toolbarGroups = [
		
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo'  ] },
		{ name: 'editing',     groups: [ 'find', 'selection' ] },
		{ name: 'links' },
 		{ name: 'insert' ,  groups: [ 'Smiley' ]  }, 
		'/',
		{ name: 'forms' },
		{ name: 'tliyoutube' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others'  },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' },
		{ name: 'strinsert' },
		{ name: 'wenzgmap'},
		//{ name: 'Smiley'},
 		 
 	];	
	}
//	    config.extraPlugins = 'youtube';

 config.extraPlugins = 'tliyoutube';
	
	//config.extraPlugins = 'wenzgmap';     /* extra*/
	//config.extraPlugins = 'tliyoutube';
	config.strinsert_button_label = 'Content Block';
    config.strinsert_button_title = config.strinsert_button_voice = 'Insert Content Block';
	
	
	config.filebrowserImageBrowseUrl = baseUrl+'/admin/index/browse';
	config.filebrowserUploadUrl = baseUrl+'/admin/index/upload';
	
	config.allowedContent=true;
	config.contenteditable="true"
	config.extraAllowedContent = 'p(*)';
	config.oembed_ShowIframePreview = true
	config.scayt_autoStartup = false;

	config.removePlugins = 'pagebreak,iframe';
	
    /*config.font_defaultLabel = 'Arial';
	config.fontSize_defaultLabel = '44px';
 	config.extraCss = "body{font-size:1.3em;}";*/
	
};

CKEDITOR.dtd.$removeEmpty.span = 0;
CKEDITOR.dtd.$removeEmpty.i = 0;


