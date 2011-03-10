/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    config.scayt_autoStartup = true;
	// Define changes to default configuration here. For example:
	config.toolbar =
	[
		['Bold' , 'Italic' , 'Link' , 'Unlink' , 'NumberedList' , 'BulletedList' , '-' , 'Spellchecker' , 'Scayt' , 'Maximize']
	];
	config.width = '380px';
    config.height = '120px';
    config.resize_enabled = false;
    
    // dialog settings
    config.dialog_backgroundCoverColor = '#000000';
    config.dialog_backgroundCoverOpacity = .5;

};
