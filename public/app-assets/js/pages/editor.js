//[editor Javascript]

//Project:	InvestX - Responsive Admin Template
//Primary use:   Used only for the wysihtml5 Editor 


//Add text editor
  

// Class definition

ClassicEditor
.create( document.querySelector( '#editor' ) )
.catch( error => {
	console.error( error );
} );